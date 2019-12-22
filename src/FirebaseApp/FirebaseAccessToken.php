<?php

namespace Firebase\FirebaseApp;

use Carbon\Carbon;

class FirebaseAccessToken {
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var Carbon
     */
    private $expirationTime;

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return FirebaseAccessToken
     */
    public function setAccessToken(string $accessToken): FirebaseAccessToken
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getExpirationTime(): Carbon
    {
        return $this->expirationTime;
    }

    /**
     * @param Carbon $expirationTime
     * @return FirebaseAccessToken
     */
    public function setExpirationTime(Carbon $expirationTime): FirebaseAccessToken
    {
        $this->expirationTime = $expirationTime;
        return $this;
    }
}
