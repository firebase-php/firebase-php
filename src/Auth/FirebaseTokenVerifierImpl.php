<?php


namespace Firebase\Auth;


use Carbon\Carbon;
use Firebase\FirebaseApp;
use Firebase\Util\Error\AuthClientErrorCode;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAuthError;
use Firebase\Util\Util;
use Firebase\Util\Validator\Validator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac;
use Lcobucci\JWT\Signer\Rsa;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;

final class FirebaseTokenVerifierImpl implements FirebaseTokenVerifier
{
    private const FIREBASE_AUDIENCE = 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit';

    private const ERROR_INVALID_CREDENTIAL = 'ERROR_INVALID_CREDENTIAL';

    private const ERROR_RUNTIME_EXCEPTION = 'ERROR_RUNTIME_EXCEPTION';

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $shortName;

    /**
     * @var string
     */
    private $articledShortName;

    /**
     * @var string
     */
    private $docUrl;

    /**
     * @var string
     */
    private $issuer;

    /**
     * @var FirebaseApp
     */
    private $app;

    /**
     * @var array
     */
    private $publicKeys;

    /**
     * @var int
     */
    private $publicKeysExpireAt;

    /**
     * @var string
     */
    private $clientCertUrl;

    public function __construct(FirebaseTokenVerifierImplBuilder $builder)
    {
        Validator::isNonEmptyString($builder->getMethod(), 'Method name must be specified');
        Validator::isNonEmptyString($builder->getShortName(), 'Short name must be specified');
        Validator::isNonEmptyString($builder->getDocUrl(), 'Doc URL must be specified');
        Validator::isURL($builder->getIssuer());
        Validator::isURL($builder->getClientCertUrl());
        $this->method = $builder->getMethod();
        $this->shortName = $builder->getShortName();
        $this->docUrl = $builder->getDocUrl();
        $this->articledShortName = $this->prefixWithIndefiniteArticle($this->shortName);
        $this->issuer = $builder->getIssuer();
        $this->app = $builder->getApp();
        $this->clientCertUrl = $builder->getClientCertUrl();
    }

    public function verifyToken(string $jwtToken)
    {
        $idToken = $this->parse($jwtToken);
        $this->checkContents($idToken);
        $this->checkSignature($idToken);
        return new FirebaseToken($idToken->getClaims());
    }

    private function prefixWithIndefiniteArticle(string $word) {
        if(strpos('aeiouAEIOU', $word[0]) === false) {
            return 'a ' . $word;
        } else {
            return 'an ' . $word;
        }
    }

    /**
     * @param string $token
     * @return \Lcobucci\JWT\Token
     * @throws FirebaseAuthException
     */
    private function parse(string $token) {
        try {
            return (new Parser())->parse($token);
        } catch (\InvalidArgumentException $e) {
            $errorMessage = sprintf(
                'Failed to parse Firebase %s. Make sure you passed a string that represents a complete and valid JWT. See %s for details on how to retrieve %s.',
                $this->shortName,
                $this->docUrl,
                $this->articledShortName);
            throw new FirebaseAuthException(self::ERROR_INVALID_CREDENTIAL, $errorMessage, $e);
        }
    }

    private function checkContents(Token $token) {
        $projectId = Util::findProjectId($this->app);
        $errorMessage = $this->getErrorIfContentInvalid($token, $projectId);

        if(is_string($errorMessage)) {
            $message = sprintf('%s %s', $errorMessage, $this->getVerifyTokenMessage());
            throw new FirebaseAuthException(self::ERROR_INVALID_CREDENTIAL, $message);
        }
    }

    private function checkSignature(Token $idToken) {
        if(!$this->isSignatureValid($idToken)) {
            throw new FirebaseAuthException(self::ERROR_INVALID_CREDENTIAL,
                sprintf(
                    'Failed to verify the signature of Firebase %s. %s',
                    $this->shortName,
                    $this->getVerifyTokenMessage()
                )
            );
        }
    }

    private function isSignatureValid(Token $idToken) {
        $publicKeys = $this->fetchPublicKeys();
        $kid = $idToken->getHeader('kid');
        if(isset($publicKeys[$kid])) {
            return $this->verifyJwtSignatureWithKey($idToken, $publicKeys[$kid]);
        } else {
            return false;
        }
    }

    private function getVerifyTokenMessage() {
        return sprintf(
            'See %s for details on how to retrieve %s.',
            $this->docUrl,
            $this->articledShortName
        );
    }

    private function getErrorIfContentInvalid(Token $idToken, string $projectId) {
        $errorMessage = null;
        if($idToken->getHeader('kid')) {
            $errorMessage = $this->getErrorForTokenWithoutKid($idToken);
        } elseif ((new Rsa\Sha256())->getAlgorithmId() !== $idToken->getHeader('algo')) {
            $errorMessage = sprintf(
                'Firebase %s has incorrect algorithm. Expected "%s" but got "%s".',
                $this->shortName,
                (new Rsa\Sha256())->getAlgorithmId(),
                $idToken->getHeader('algo')
            );
        } elseif ($idToken->getClaim('aud') !== $projectId) {
            $errorMessage = sprintf(
                'Firebase %s has incorrect "aud" (audience) claim. Expected "%s" but got "%s". %s',
                $this->shortName,
                $projectId,
                $idToken->getClaim('aud'),
                $this->getProjectIdMatchMessage()
            );
        } elseif ($idToken->getClaim('iss') !== $this->issuer . $projectId) {
            $errorMessage = sprintf(
                'Firebase %s has incorrect "iss" (issuer) claim. Expected "%s" but got "%s". %s',
                $this->shortName,
                $this->issuer . $projectId,
                $idToken->getClaim('iss'),
                $this->getProjectIdMatchMessage()
            );
        } elseif (!is_string($idToken->getClaim('sub'))) {
            $errorMessage = sprintf(
                'Firebase %s has no "sub (subject) claim.',
                $this->shortName
            );
        } elseif (empty($idToken->getClaim('sub'))) {
            $errorMessage = sprintf(
                'Firebase %s has an empty string "sub" (subject) claim.',
                $this->shortName
            ) ;
        } elseif (strlen($idToken->getClaim('sub')) > 128) {
            $errorMessage = sprintf(
                'Firebase %s has "sub" (subject) claim longer than 128 characters.',
                $this->shortName
            );
        }

        return $errorMessage;
    }

    private function getErrorForTokenWithoutKid(Token $idToken) {
        if($this->isCustomToken($idToken)) {
            return sprintf('%s expects %s, but was given a custom token.', $this->method, $this->articledShortName);
        } elseif($this->isLegacyCustomToken($idToken)) {
            return sprintf('%s expects %s, but was given a legacy custom token.', $this->method, $this->articledShortName);
        }
        return sprintf('Firebase %s has not "kid" claim.', $this->shortName);
    }

    /**
     * @param Token $idToken
     * @return bool
     */
    private function isCustomToken(Token $idToken): bool {
        return self::FIREBASE_AUDIENCE === $idToken->getClaim('aud');
    }

    /**
     * @param Token $idToken
     * @return bool
     */
    private function isLegacyCustomToken(Token $idToken): bool {
        $algorithm = $idToken->getHeader('algo');
        $v = intval($idToken->getClaim('v'));
        return $algorithm === (new Hmac\Sha256())->getAlgorithmId() && $v === 0 && $this->containsLegacyUidField($idToken);
    }

    /**
     * @param Token $idToken
     * @return bool
     */
    private function containsLegacyUidField(Token $idToken): bool {
        $dataField = $idToken->getClaim('d');
        if(is_array($dataField)) {
            return isset($dataField['uid']);
        }
        return false;
    }

    private function getProjectIdMatchMessage() {
        return sprintf('Make sure the %s comes from the same Firebase project as the service account used to  authenticate this SDK.'. $this->shortName);
    }

    /**
     * @return array
     * @throws FirebaseAuthException
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
                    $subParts = explode('=', trim($part));
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

                throw new FirebaseAuthException(self::ERROR_RUNTIME_EXCEPTION, $errorMessage);
            }
            throw new FirebaseAuthException(self::ERROR_RUNTIME_EXCEPTION, 'Error while verifying signature.', $e);
        }
    }

    private function verifyJwtSignatureWithKey(Token $idToken, string $publicKey): bool {
        return $idToken->verify(new Sha256(), $publicKey) && $idToken->validate(new ValidationData());
    }
}
