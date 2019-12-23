<?php


namespace Firebase\Auth\TokenVerifier;

use Carbon\Carbon;
use Firebase\Auth\FirebaseAuth\DecodedIdToken;
use Firebase\FirebaseApp;
use Firebase\Util\Error\AuthClientErrorCode;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAuthError;
use Firebase\Util\Util;
use Firebase\Util\Validator\Validator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;

/**
 * Class for verifying general purpose Firebase JWTs. This verifies ID tokens and session cookies.
 *
 * Class FirebaseTokenVerifier
 * @package Firebase\Auth\TokenVerifier
 */
class FirebaseTokenVerifier
{
    private $publicKeys;

    private $publicKeysExpireAt;

    private $shortNameArticle;

    private $clientCertUrl;

    private $algorithm;

    private $issuer;

    private $tokenInfo;

    private $app;

    public function __construct(
        string $clientCertUrl,
        string $algorithm,
        string $issuer,
        FirebaseTokenInfo $tokenInfo,
        FirebaseApp $app
    )
    {
        $this->clientCertUrl = $clientCertUrl;
        $this->algorithm = $algorithm;
        $this->issuer = $issuer;
        $this->tokenInfo = $tokenInfo;
        $this->app = $app;

        if(!Validator::isURL($clientCertUrl)) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
                'The provided public client certificate URL is an invalid URL.'
            );
        } elseif (!Validator::isNonEmptyString($algorithm)) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
                'The provided JWT algorithm is an empty string.'
            );
        } elseif (!Validator::isURL($issuer)) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
                'The provided JWT issuer is an invalid URL.'
            );
        } elseif (!Validator::isNonNullObject($tokenInfo)) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
                'The provided JWT information is not an object or null.'
            );
        } elseif (!Validator::isURL($tokenInfo->getUrl())) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
                'The provided JWT verification documentation URL is invalid.'
            );
        } elseif (!Validator::isNonEmptyString($tokenInfo->getVerifyApiName())) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
                'The JWT verify API name must be a non-empty string.'
            );
        } elseif (!Validator::isNonEmptyString($tokenInfo->getJwtName())) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
                'The JWT public full name must be a non-empty string.'
            );
        } elseif (!Validator::isNonEmptyString($tokenInfo->getShortName())) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
                'The JWT public short name must be a non-empty string.'
            );
        } elseif (
            !Validator::isNonNullObject($tokenInfo->getExpiredErrorCode())
            || !Validator::isNonEmptyString($tokenInfo->getExpiredErrorCode()->getCode())
        ) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
                'The JWT expiration error code must be a non-null ErrorInfo object.'
            );
        }

        preg_match('/[aeiou]/i', $this->tokenInfo->getShortName()[0], $matches);
        $this->shortNameArticle = empty($matches) ? 'a' : 'an';
    }

    /**
     * @param string $jwtToken
     * @return array An array fulfilled with the decoded claims of the DecodedIdToken.
     * @see DecodedIdToken
     * @throws FirebaseAuthError
     */
    public function verifyJWT(string $jwtToken): array {
        if(!Validator::isString($jwtToken)) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
                'First argument to ' . $this->tokenInfo->getVerifyApiName() . ' must be a ' . $this->tokenInfo->getJwtName() . ' string.'
            );
        }

        $projectId = Util::findProjectId($this->app);
        return $this->verifyJWTWithProjectId($jwtToken, $projectId);
    }

    /**
     * @param string $jwtToken
     * @param string|null $projectId
     * @return array
     * @see DecodedIdToken
     * @throws FirebaseAuthError
     */
    private function verifyJWTWithProjectId(string $jwtToken, string $projectId = null) : array {
        if(!Validator::isNonEmptyString($projectId)) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_CREDENTIAL['code']),
                'Must initialize app with a cert credential or set your Firebase project ID as the GOOGLE_CLOUD_PROJECT environment variable to call ' . $this->tokenInfo->getVerifyApiName()
            );
        }

        $decodedToken = (new Parser())->parse($jwtToken);
        $header = $decodedToken->getHeaders();
        $payload = $decodedToken->getClaims();
        $projectIdMatchMessage = sprintf('Make sure the %s comes from the same Firebase project as the service account used to authenticate this SDK.', $this->tokenInfo->getShortName());
        $verifyJwtTokenDocsMessage = sprintf('See %s for details on how to retrieve %s %s.', $this->tokenInfo->getUrl(), $this->shortNameArticle, $this->tokenInfo->getShortName());

        $errorMessage = null;
        if(!$decodedToken) {
            $errorMessage = sprintf('Decoding %s failed. Make sure you passed the entire string JWT which represents %s %s. %s', $this->tokenInfo->getJwtName(), $this->shortNameArticle, $this->tokenInfo->getShortName(), $verifyJwtTokenDocsMessage);
        } elseif(!isset($header['kid'])) {
            $isCustomToken = $payload['aud'] === TokenVerifierHelpers::FIREBASE_AUDIENCE;
            $isLegacyCustomToken = ($header['alg'] === 'HS256' && $payload['v'] === 0 && isset($payload['d']));

            if($isCustomToken) {
                $errorMessage = sprintf('%s expects %s %s, but was given a custom token.', $this->tokenInfo->getVerifyApiName(), $this->shortNameArticle, $this->tokenInfo->getShortName());
            } elseif ($isLegacyCustomToken) {
                $errorMessage = sprintf('%s expects %s %s, but was given a legacy custom token.', $this->tokenInfo->getVerifyApiName(), $this->shortNameArticle, $this->tokenInfo->getShortName());
            } else {
                $errorMessage = 'Firebase ID token has no "kid" claim.';
            }

            $errorMessage .= $verifyJwtTokenDocsMessage;
        } elseif ($header['alg'] !== $this->algorithm) {
            $errorMessage = sprintf('%s has incorrect algorithm. Expected %s but got %s. %s', $this->tokenInfo->getJwtName(), $this->algorithm, $header['alg'], $verifyJwtTokenDocsMessage);
        } elseif ($payload['aud'] !== $projectId) {
            $errorMessage = sprintf('%s has incorrect "aud" (audience) claim. Expected %s%s but got %s. %s', $this->tokenInfo->getJwtName(), $this->issuer, $projectId, $payload['aud'], $verifyJwtTokenDocsMessage);
        } elseif ($payload['iss'] !== $this->issuer . $projectId) {
            $errorMessage = sprintf('%s has incorrect "iss" (issuer) claim. Expected %s%s but got %s. %s %s', $this->tokenInfo->getJwtName(), $this->issuer, $projectId, $payload['iss'], $projectIdMatchMessage, $verifyJwtTokenDocsMessage);
        } elseif (!is_string($payload['sub'])) {
            $errorMessage = sprintf('%s has no "sub" (subject) claim. %s', $this->tokenInfo->getJwtName(), $verifyJwtTokenDocsMessage);
        } elseif ($payload['sub'] === '') {
            $errorMessage = sprintf('%s has an empty string "sub" (subject) claim. %s', $this->tokenInfo->getJwtName(), $verifyJwtTokenDocsMessage);
        } elseif (strlen($payload['sub']) > 128) {
            $errorMessage = sprintf('%s has "sub" (subject) claim longer than 128 characters. %s', $this->tokenInfo->getJwtName(), $verifyJwtTokenDocsMessage);
        }
        if($errorMessage) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
                $errorMessage
            );
        }

        $publicKeys = $this->fetchPublicKeys();
        if(!isset($publicKeys[$header['kid']])) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
                sprintf('%s has "kid" claim which does not correspond to a known public key. Most likely the %s is expired, so get a fresh token from your client app and try again.', $this->tokenInfo->getJwtName(), $this->tokenInfo->getShortName())
            );
        } else {
            return $this->verifyJwtSignatureWithKey($jwtToken, $publicKeys[$header['kid']]);
        }
    }

    private function verifyJwtSignatureWithKey(string $jwtToken, string $publicKey): array {
        $verifyJwtTokenDocsMessage = sprintf('See %s for details on how to retrieve % %s.', $this->tokenInfo->getUrl(), $this->shortNameArticle, $this->tokenInfo->getShortName());
        $token = (new Parser())->parse($jwtToken);
        $errorMessage = null;
        if($token->verify(new Sha256(), $publicKey)) {
            if($token->validate(new ValidationData())) {
                $payload = $token->getClaims();
                $payload['uid'] = $payload['sub'];
                return $payload;
            } else {
                $errorMessage = sprintf('%s has expired. Get a fresh %s from your client app and try again (auth/%s). %s', $this->tokenInfo->getJwtName(), $this->tokenInfo->getShortName(), $this->tokenInfo->getExpiredErrorCode()->getCode(), $verifyJwtTokenDocsMessage);
            }
        } else {
            $errorMessage = sprintf('%s has invalid signature. %s', $this->tokenInfo->getJwtName(), $verifyJwtTokenDocsMessage);
        }

        throw new FirebaseAuthError(
            new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
            $errorMessage
        );
    }

    /**
     * @return array
     * @throws FirebaseAuthError
     */
    private function fetchPublicKeys(): array {
        $publicKeysExist = isset($this->publicKeys);
        $publicKeysExpiredExist = isset($this->publicKeysExpireAt);
        $publicKeysStillValid = ($publicKeysExpiredExist && Carbon::now()->getTimestamp() < $this->publicKeysExpireAt);
        if($publicKeysExist && $publicKeysStillValid) {
            return $this->publicKeys;
        }

        $request = new Request('GET', $this->clientCertUrl);

        try {
            $response = (new Client())->send($request);
            $body = json_decode($response->getBody());
            if(isset($body['error'])) {
                throw new ClientException('', $request, $response);
            }

            $headers = $response->getHeaders();

            if(isset($headers['cache-control'])) {
                $cacheControlHeader = $headers['cache-control'];
                $parts = explode(',', $cacheControlHeader);
                foreach ($parts as $part) {
                    $subParts = explod('=', trim($part));
                    if($subParts[0] === 'max-age') {
                        $maxAge = +$subParts[1];
                        $this->publicKeysExpireAt = Carbon::now()->getTimestamp() + $maxAge * 1000;
                    }
                }
            }
            $this->publicKeys = $body;
            return $body;
        } catch (\Exception $e) {
            if($e instanceof ClientException) {
                $errorMessage = 'Error fetching public keys for Google certs: ';
                $response = $e->getResponse();
                $body = json_decode($response->getBody());
                if(isset($body['error'])) {
                    $errorMessage .= $body['error'];
                    if(isset($body['error_description'])) {
                        $errorMessage .= sprintf(' (%s)', $body['error_description']);
                    }
                }

                throw new FirebaseAuthError(
                    new ErrorInfo(AuthClientErrorCode::INTERNAL_ERROR['code']),
                    $errorMessage
                );
            }
            throw $e;
        }
    }
}
