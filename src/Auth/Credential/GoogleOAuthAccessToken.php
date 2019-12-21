<?php

namespace Firebase\Auth\Credential;

class GoogleOAuthAccessToken {
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var int
     */
    private $expiresIn;

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return GoogleOAuthAccessToken
     */
    public function setAccessToken(string $accessToken): GoogleOAuthAccessToken
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    /**
     * @param int $expiresIn
     * @return GoogleOAuthAccessToken
     */
    public function setExpiresIn(int $expiresIn): GoogleOAuthAccessToken
    {
        $this->expiresIn = $expiresIn;
        return $this;
    }
}
