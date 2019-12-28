<?php


namespace Firebase\Auth;

class SessionCookieOptionsBuilder
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
     * @return SessionCookieOptionsBuilder
     */
    public function setExpiresIn(int $expiresInMilli)
    {
        $this->expiresIn = $expiresInMilli;
        return $this;
    }

    public function build()
    {
        return new SessionCookieOptions($this);
    }
}
