<?php


namespace Firebase\Auth;

use Carbon\Carbon;
use Firebase\Auth\Internal\GooglePublicKeysManager;
use Firebase\Auth\Internal\IdTokenVerifier;
use Firebase\Util\Validator\Validator;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac;
use Lcobucci\JWT\Signer\Rsa;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;

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
     * @var GooglePublicKeysManager
     */
    private $publicKeysManager;

    /**
     * @var IdTokenVerifier
     */
    private $idTokenVerifier;

    public static function builder() {
        return new FirebaseTokenVerifierImplBuilder();
    }

    public function __construct(FirebaseTokenVerifierImplBuilder $builder)
    {
        $this->method = Validator::isNonEmptyString($builder->getMethod(), 'Method name must be specified');
        $this->shortName = Validator::isNonEmptyString($builder->getShortName(), 'Short name must be specified');
        $this->docUrl = Validator::isNonEmptyString($builder->getDocUrl(), 'Doc URL must be specified');
        $this->publicKeysManager = Validator::isNonNullObject($builder->getPublicKeysManager());
        $this->idTokenVerifier = Validator::isNonNullObject($builder->getIdTokenVerifier());
        $this->articledShortName = $this->prefixWithIndefiniteArticle($this->shortName);
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
        } catch (\Exception $e) {
            $errorMessage = sprintf(
                'Failed to parse Firebase %s. Make sure you passed a string that represents a complete and valid JWT. See %s for details on how to retrieve %s.',
                $this->shortName,
                $this->docUrl,
                $this->articledShortName);
            throw new FirebaseAuthException(self::ERROR_INVALID_CREDENTIAL, $errorMessage, $e);
        }
    }

    private function checkContents(Token $token) {
        $errorMessage = $this->getErrorIfContentInvalid($token);

        if(is_string($errorMessage)) {
            $message = sprintf('%s %s', $errorMessage, $this->getVerifyTokenMessage());
            throw new FirebaseAuthException(self::ERROR_INVALID_CREDENTIAL, $message);
        }
    }

    private function checkSignature(Token $idToken) {
        try {

            if(!$this->isSignatureValid($idToken)) {
                throw new FirebaseAuthException(self::ERROR_INVALID_CREDENTIAL,
                    sprintf(
                        'Failed to verify the signature of Firebase %s. %s',
                        $this->shortName,
                        $this->getVerifyTokenMessage()
                    )
                );
            }
        } catch (\Exception $e) {
            if(strpos($e->getMessage(), $this->shortName) === false) {
                throw new FirebaseAuthException(
                    self::ERROR_RUNTIME_EXCEPTION,
                    'Error while verifying signature',
                    $e
                );
            }
            throw $e;
        }
    }

    private function isSignatureValid(Token $idToken) {
        $publicKeys = $this->publicKeysManager->getPublicKeys();
        foreach($publicKeys as $key) {
            if($idToken->verify(new Sha256(), $key)) {
                return true;
            }
        }

        return false;
    }

    private function getVerifyTokenMessage() {
        return sprintf(
            'See %s for details on how to retrieve %s.',
            $this->docUrl,
            $this->articledShortName
        );
    }

    private function getErrorIfContentInvalid(Token $idToken) {
        $errorMessage = null;
        if(!$idToken->hasHeader('kid') || empty($idToken->getHeader('kid'))) {
            $errorMessage = $this->getErrorForTokenWithoutKid($idToken);
        } elseif ((new Rsa\Sha256())->getAlgorithmId() !== $idToken->getHeader('alg')) {
            $errorMessage = sprintf(
                'Firebase %s has incorrect algorithm. Expected "%s" but got "%s".',
                $this->shortName,
                (new Rsa\Sha256())->getAlgorithmId(),
                $idToken->getHeader('alg')
            );
        } elseif ($idToken->getClaim('aud') !== $this->idTokenVerifier->getAudience()) {
            $errorMessage = sprintf(
                'Firebase %s has incorrect "aud" (audience) claim. Expected "%s" but got "%s". %s',
                $this->shortName,
                $this->idTokenVerifier->getAudience(),
                $idToken->getClaim('aud'),
                $this->getProjectIdMatchMessage()
            );
        } elseif ($idToken->getClaim('iss') !== $this->idTokenVerifier->getIssuer()) {
            $errorMessage = sprintf(
                'Firebase %s has incorrect "iss" (issuer) claim. Expected "%s" but got "%s". %s',
                $this->shortName,
                $this->idTokenVerifier->getIssuer(),
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
        } elseif (!$this->verifyTimestamp($idToken)) {
            $errorMessage = sprintf(
                'Firebase %s has expired or is not yet valid. Get a fresh %s and try again.',
                $this->shortName,
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
        return sprintf('Firebase %s has no "kid" claim.', $this->shortName);
    }

    /**
     * @param Token $idToken
     * @return bool
     */
    private function isCustomToken(Token $idToken): bool {
        return self::FIREBASE_AUDIENCE === $idToken->getClaim('aud', false);
    }

    /**
     * @param Token $idToken
     * @return bool
     */
    private function isLegacyCustomToken(Token $idToken): bool {
        $algorithm = $idToken->getHeader('alg', false);
        $v = intval($idToken->getClaim('v', -1));
        return $algorithm === (new Hmac\Sha256())->getAlgorithmId() && $v === 0 && $this->containsLegacyUidField($idToken);
    }

    /**
     * @param Token $idToken
     * @return bool
     */
    private function containsLegacyUidField(Token $idToken): bool {
        $dataField = $idToken->getClaim('d', false);
        if(is_array($dataField)) {
            return isset($dataField['uid']);
        }
        if(is_object($dataField)) {
            return isset($dataField->uid);
        }
        return false;
    }

    private function getProjectIdMatchMessage() {
        return sprintf('Make sure the %s comes from the same Firebase project as the service account used to  authenticate this SDK.', $this->shortName);
    }

    private function verifyTimestamp(Token $idToken) {
        $iat = intval($idToken->getClaim('iat', -1));
        $currentTimeSeconds = Carbon::now()->timestamp;
        if($iat === -1 || $iat > $currentTimeSeconds) {
            return false;
        }
        $nowLeeway = $currentTimeSeconds - $this->idTokenVerifier->getAcceptableTimeSkewSeconds();
        $now = Carbon::createFromTimestamp($nowLeeway);
        return !$idToken->isExpired($now);
    }
}
