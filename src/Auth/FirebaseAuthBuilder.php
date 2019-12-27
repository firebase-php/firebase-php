<?php


namespace Firebase\Auth;


use Firebase\Auth\Internal\FirebaseTokenFactory;
use Firebase\FirebaseApp;

class FirebaseAuthBuilder
{
    /**
     * @var FirebaseApp
     */
    private $firebaseApp;

    /**
     * @var FirebaseTokenFactory
     */
    private $tokenFactory;

    /**
     * @var FirebaseTokenVerifier
     */
    private $idTokenVerifier;

    /**
     * @var FirebaseTokenVerifier
     */
    private $cookieTokenVerifier;

    /**
     * @return FirebaseApp
     */
    public function getFirebaseApp(): FirebaseApp
    {
        return $this->firebaseApp;
    }

    /**
     * @param FirebaseApp $firebaseApp
     * @return FirebaseAuthBuilder
     */
    public function setFirebaseApp(FirebaseApp $firebaseApp): FirebaseAuthBuilder
    {
        $this->firebaseApp = $firebaseApp;
        return $this;
    }

    /**
     * @return FirebaseTokenFactory
     */
    public function getTokenFactory(): ?FirebaseTokenFactory
    {
        return $this->tokenFactory;
    }

    /**
     * @param FirebaseTokenFactory $tokenFactory
     * @return FirebaseAuthBuilder
     */
    public function setTokenFactory(?FirebaseTokenFactory $tokenFactory): FirebaseAuthBuilder
    {
        $this->tokenFactory = $tokenFactory;
        return $this;
    }

    /**
     * @return FirebaseTokenVerifier
     */
    public function getIdTokenVerifier(): ?FirebaseTokenVerifier
    {
        return $this->idTokenVerifier;
    }

    /**
     * @param FirebaseTokenVerifier $idTokenVerifier
     * @return FirebaseAuthBuilder
     */
    public function setIdTokenVerifier(?FirebaseTokenVerifier $idTokenVerifier): FirebaseAuthBuilder
    {
        $this->idTokenVerifier = $idTokenVerifier;
        return $this;
    }

    /**
     * @return FirebaseTokenVerifier
     */
    public function getCookieTokenVerifier(): ?FirebaseTokenVerifier
    {
        return $this->cookieTokenVerifier;
    }

    /**
     * @param FirebaseTokenVerifier $cookieTokenVerifier
     * @return FirebaseAuthBuilder
     */
    public function setCookieTokenVerifier(?FirebaseTokenVerifier $cookieTokenVerifier): FirebaseAuthBuilder
    {
        $this->cookieTokenVerifier = $cookieTokenVerifier;
        return $this;
    }

    public function build() {
        return new FirebaseAuth($this);
    }
}
