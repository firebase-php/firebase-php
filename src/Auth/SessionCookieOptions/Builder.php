<?php


namespace Firebase\Auth\SessionCookieOptions;


use Firebase\Auth\SessionCookieOptions;

class Builder
{
    /**
     * @var int
     */
    private $expiresIn;

    /**
     * @return int
     */
    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    /**
     * @param int $expiresInMilli
     * @return Builder
     */
    public function setExpiresIn(int $expiresInMilli)
    {
        $this->expiresIn = $expiresInMilli;
        return $this;
    }

    public function build() {
        return new SessionCookieOptions($this);
    }
}