<?php


namespace Firebase\Auth;

use Firebase\Util\Validator\Validator;

class RevocationCheckDecorator implements FirebaseTokenVerifier
{
    const ID_TOKEN_REVOKED_ERROR = 'id-token-revoked';

    const SESSION_COOKIE_REVOKED_ERROR = 'session-cookie-revoked';

    /**
     * @var FirebaseTokenVerifier
     */
    private $tokenVerifier;

    /**
     * @var FirebaseUserManager
     */
    private $userManager;

    /**
     * @var string
     */
    private $errorCode;

    /**
     * @var string
     */
    private $shortName;

    public function __construct(
        FirebaseTokenVerifier $tokenVerifier,
        FirebaseUserManager $userManager,
        string $errorCode,
        string $shortName
    ) {
        $this->tokenVerifier = Validator::isNonNullObject($tokenVerifier);
        $this->userManager = Validator::isNonNullObject($userManager);
        $this->errorCode = Validator::isNonEmptyString($errorCode);
        $this->shortName = Validator::isNonEmptyString($shortName);
    }

    public function verifyToken(string $token)
    {
        $firebaseToken = $this->tokenVerifier->verifyToken($token);
        if ($this->isRevoked($firebaseToken)) {
            throw new FirebaseAuthException(
                $this->errorCode,
                sprintf('Firebase %s revoked', $this->shortName)
            );
        }

        return $firebaseToken;
    }

    private function isRevoked(FirebaseToken $firebaseToken)
    {
        $user = $this->userManager->getUserById($firebaseToken->getUid());
        $issuedAtInSeconds = intval($firebaseToken->getClaims()['iat']);
        return $user->getTokensValidAfterTimestamp() > $issuedAtInSeconds * 1000;
    }

    public static function decorateIdTokenVerifier(
        FirebaseTokenVerifier $tokenVerifier,
        FirebaseUserManager $userManager
    ) {
        return new RevocationCheckDecorator($tokenVerifier, $userManager, self::ID_TOKEN_REVOKED_ERROR, 'id token');
    }

    public static function decorateSessionCookieVerifier(
        FirebaseTokenVerifier $tokenVerifier,
        FirebaseUserManager $userManager
    ) {
        return new RevocationCheckDecorator($tokenVerifier, $userManager, self::SESSION_COOKIE_REVOKED_ERROR, 'session cookie');
    }
}
