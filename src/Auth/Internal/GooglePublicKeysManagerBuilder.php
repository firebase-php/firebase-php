<?php


namespace Firebase\Auth\Internal;


class GooglePublicKeysManagerBuilder
{
    private $publicCertsEncodeUrl;

    public function __construct()
    {
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
}
