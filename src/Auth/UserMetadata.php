<?php


namespace Firebase\Auth;


class UserMetadata
{
    private $creationTimestamp;

    private $lastSignInTimestamp;

    /**
     * UserMetadata constructor.
     * @param int $creationTimestamp
     * @param int|null $lastSignInTimestamp
     */
    public function __construct(int $creationTimestamp, int $lastSignInTimestamp = null)
    {
        $this->creationTimestamp = $creationTimestamp;
        $this->lastSignInTimestamp = $lastSignInTimestamp;
    }

    /**
     * @return int
     */
    public function getCreationTimestamp(): int
    {
        return $this->creationTimestamp;
    }

    /**
     * @return int|null
     */
    public function getLastSignInTimestamp(): ?int
    {
        return $this->lastSignInTimestamp;
    }
}
