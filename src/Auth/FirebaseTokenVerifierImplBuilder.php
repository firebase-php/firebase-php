<?php


namespace Firebase\Auth;


use Firebase\FirebaseApp;

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
     * @var string
     */
    private $clientCertUrl;

    /**
     * @var string
     */
    private $algorithm;

    /**
     * @var string
     */
    private $issuer;

    /**
     * @var FirebaseApp
     */
    private $app;

    public function builder() {
        return new FirebaseTokenVerifierImpl($this);
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return FirebaseTokenVerifierImplBuilder
     */
    public function setMethod(string $method): FirebaseTokenVerifierImplBuilder
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @param string $shortName
     * @return FirebaseTokenVerifierImplBuilder
     */
    public function setShortName(string $shortName): FirebaseTokenVerifierImplBuilder
    {
        $this->shortName = $shortName;
        return $this;
    }

    /**
     * @return string
     */
    public function getDocUrl(): string
    {
        return $this->docUrl;
    }

    /**
     * @param string $docUrl
     * @return FirebaseTokenVerifierImplBuilder
     */
    public function setDocUrl(string $docUrl): FirebaseTokenVerifierImplBuilder
    {
        $this->docUrl = $docUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientCertUrl(): string
    {
        return $this->clientCertUrl;
    }

    /**
     * @param string $clientCertUrl
     * @return FirebaseTokenVerifierImplBuilder
     */
    public function setClientCertUrl(string $clientCertUrl): FirebaseTokenVerifierImplBuilder
    {
        $this->clientCertUrl = $clientCertUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    /**
     * @param string $algorithm
     * @return FirebaseTokenVerifierImplBuilder
     */
    public function setAlgorithm(string $algorithm): FirebaseTokenVerifierImplBuilder
    {
        $this->algorithm = $algorithm;
        return $this;
    }

    /**
     * @return string
     */
    public function getIssuer(): string
    {
        return $this->issuer;
    }

    /**
     * @param string $issuer
     * @return FirebaseTokenVerifierImplBuilder
     */
    public function setIssuer(string $issuer): FirebaseTokenVerifierImplBuilder
    {
        $this->issuer = $issuer;
        return $this;
    }

    /**
     * @return FirebaseApp
     */
    public function getApp(): FirebaseApp
    {
        return $this->app;
    }

    /**
     * @param FirebaseApp $app
     * @return FirebaseTokenVerifierImplBuilder
     */
    public function setApp(FirebaseApp $app): FirebaseTokenVerifierImplBuilder
    {
        $this->app = $app;
        return $this;
    }
}