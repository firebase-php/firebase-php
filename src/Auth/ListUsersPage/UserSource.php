<?php


namespace Firebase\Auth\ListUsersPage;

/**
 * Represents a source of user data that can be queried to load a batch of users.
 */
interface UserSource
{
    public function fetch(int $maxResults, ?string $pageToken): ListUsersResult;
}
