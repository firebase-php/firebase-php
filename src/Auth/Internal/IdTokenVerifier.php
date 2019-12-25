<?php


namespace Firebase\Auth\Internal;

use Firebase\Util\Validator\Validator;
use Google\Auth\OAuth2;

class IdTokenVerifier extends OAuth2 {
    const DEFAULT_TIME_SKEW_SECONDS = 300;

    /**
     * @var int
     */
    private $acceptableTimeSkewSeconds;

    public function __construct(?array $config = [])
    {
        parent::__construct($config);
        $this->acceptableTimeSkewSeconds = self::DEFAULT_TIME_SKEW_SECONDS;
    }

    /**
     * @return int
     */
    public function getAcceptableTimeSkewSeconds(): int
    {
        return $this->acceptableTimeSkewSeconds;
    }

    /**
     * @param int $acceptableTimeSkewSeconds
     * @return IdTokenVerifier
     */
    public function setAcceptableTimeSkewSeconds(int $acceptableTimeSkewSeconds): IdTokenVerifier
    {
        Validator::checkArgument($acceptableTimeSkewSeconds >= 0);
        $this->acceptableTimeSkewSeconds = $acceptableTimeSkewSeconds;
        return $this;
    }
}
