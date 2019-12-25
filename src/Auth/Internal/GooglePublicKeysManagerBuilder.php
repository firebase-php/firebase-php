<?php


namespace Firebase\Auth\Internal;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class GooglePublicKeysManagerBuilder
{
    private $publicCertsEncodeUrl;

    private $httpClient;

    public function __construct(?ClientInterface $httpClient = null)
    {
        if(!$httpClient) {
            $this->httpClient = new Client();
        } else {
            $this->httpClient = $httpClient;
        }
        $this->publicCertsEncodeUrl = 'https://www.googleapis.com/oauth2/v1/certs';
    }

    public function build() {
        return new GooglePublicKeysManager($this);
    }

    /**
     * @return string
     */
    public function getPublicCertsEncodeUrl(): string
    {
        return $this->publicCertsEncodeUrl;
    }

    /**
     * @param string $publicCertsEncodeUrl
     * @return GooglePublicKeysManagerBuilder
     */
    public function setPublicCertsEncodeUrl(string $publicCertsEncodeUrl): GooglePublicKeysManagerBuilder
    {
        $this->publicCertsEncodeUrl = $publicCertsEncodeUrl;
        return $this;
    }

    /**
     * @return ClientInterface|null
     */
    public function getHttpClient(): ?ClientInterface
    {
        return $this->httpClient;
    }

    /**
     * @param ClientInterface|null $httpClient
     * @return GooglePublicKeysManagerBuilder
     */
    public function setHttpClient(?ClientInterface $httpClient): GooglePublicKeysManagerBuilder
    {
        $this->httpClient = $httpClient;
        return $this;
    }
}
