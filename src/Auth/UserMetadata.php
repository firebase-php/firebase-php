<?php

namespace Firebase\Auth;

class UserMetadata
{
    /**
     * @var int
     */
    private $creationTimestamp;

    /**
     * @var int
     */
    private $lastSignInTimestamp;

    public function __construct(int $creationTimestamp, int $lastSignInTimestamp = null)
    {
        $this->creationTimestamp = $creationTimestamp;
        if(!is_null($lastSignInTimestamp)) {
            $this->lastSignInTimestamp = $lastSignInTimestamp;
        }
    }

    /**
     * @return int
     */
    public function getCreationTimestamp(): int
    {
        return $this->creationTimestamp;
    }

    /**
     * @return int
     */
    public function getLastSignInTimestamp(): int
    {
        return $this->lastSignInTimestamp;
    }
}
