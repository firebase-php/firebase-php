<?php


namespace Firebase\Auth\FirebaseAuth;


use Firebase\Auth\TokenGenerator\FirebaseTokenGenerator;
use Firebase\Auth\TokenGenerator\TokenGeneratorHelpers;
use Firebase\Auth\TokenVerifier\FirebaseTokenVerifier;
use Firebase\Auth\TokenVerifier\TokenVerifierHelpers;
use Firebase\FirebaseApp;

class BaseAuth
{
    protected $tokenGenerator;

    /**
     * @var FirebaseTokenVerifier
     */
    protected $idTokenVerifier;

    protected $sessionCookieVerifier;

    public function __construct(FirebaseApp $app, FirebaseTokenGenerator $tokenGenerator = null)
    {
        if($tokenGenerator) {
            $this->tokenGenerator = $tokenGenerator;
        } else {
            $cryptoSigner = TokenGeneratorHelpers::cryptoSignerFromApp($app);
            $this->tokenGenerator = new FirebaseTokenGenerator($cryptoSigner);
        }

        $this->sessionCookieVerifier = TokenVerifierHelpers::createSessionCookieVerifier($app);
        $this->idTokenVerifier = TokenVerifierHelpers::createIdTokenVerifier($app);
    }

    public function createCustomToken(string $uid, array $developerClaims = null): string {
        return $this->tokenGenerator->createCustomToken($uid, $developerClaims);
    }

    public function verifyIdToken(string $idToken, bool $checkRevoked = false): array {
        return $this->idTokenVerifier->verifyJWT($idToken);
    }
}