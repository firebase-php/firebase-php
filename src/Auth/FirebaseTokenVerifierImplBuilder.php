<?php


namespace Firebase\Auth;

use Firebase\Auth\Internal\GooglePublicKeysManager;
use Firebase\Auth\Internal\IdTokenVerifier;

class FirebaseTokenVerifierImplBuilder
{
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
    private $docUrl;

    /**
     * @var GooglePublicKeysManager
     */
    private $publicKeysManager;

    /**
     * @var IdTokenVerifier
     */
    private $idTokenVerifier;

    public function build()
    {
        return new FirebaseTokenVerifierImpl($this);
    }

    /**
     * @return string
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return FirebaseTokenVerifierImplBuilder
     */
    public function setMethod(?string $method): FirebaseTokenVerifierImplBuilder
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    /**
     * @param string $shortName
     * @return FirebaseTokenVerifierImplBuilder
     */
    public function setShortName(?string $shortName): FirebaseTokenVerifierImplBuilder
    {
        $this->shortName = $shortName;
        return $this;
    }

    /**
     * @return string
     */
    public function getDocUrl(): ?string
    {
        return $this->docUrl;
    }

    /**
     * @param string $docUrl
     * @return FirebaseTokenVerifierImplBuilder
     */
    public function setDocUrl(?string $docUrl): FirebaseTokenVerifierImplBuilder
    {
        $this->docUrl = $docUrl;
        return $this;
    }

    /**
     * @return GooglePublicKeysManager
     */
    public function getPublicKeysManager(): ?GooglePublicKeysManager
    {
        return $this->publicKeysManager;
    }

    /**
     * @param GooglePublicKeysManager $publicKeysManager
     * @return FirebaseTokenVerifierImplBuilder
     */
    public function setPublicKeysManager(?GooglePublicKeysManager $publicKeysManager): FirebaseTokenVerifierImplBuilder
    {
        $this->publicKeysManager = $publicKeysManager;
        return $this;
    }

    /**
     * @return IdTokenVerifier
     */
    public function getIdTokenVerifier(): ?IdTokenVerifier
    {
        return $this->idTokenVerifier;
    }

    /**
     * @param IdTokenVerifier $idTokenVerifier
     * @return FirebaseTokenVerifierImplBuilder
     */
    public function setIdTokenVerifier(?IdTokenVerifier $idTokenVerifier): FirebaseTokenVerifierImplBuilder
    {
        $this->idTokenVerifier = $idTokenVerifier;
        return $this;
    }
}
