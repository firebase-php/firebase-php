<?php


namespace Firebase\Auth\ListUsersPage;

use Firebase\Auth\ExportedUserRecord;

final class ListUsersResult
{
    /**
     * @var ExportedUserRecord[]
     */
    private $users;

    /**
     * @var string
     */
    private $nextPageToken;

    /**
     * @param ExportedUserRecord[] $users
     * @param string $nextPageToken
     */
    public function __construct(array $users, string $nextPageToken)
    {
        $this->users = $users;
        $this->nextPageToken = $nextPageToken;
    }

    /**
     * @return ExportedUserRecord[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @return string
     */
    public function getNextPageToken(): string
    {
        return $this->nextPageToken;
    }
}
