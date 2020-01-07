<?php


namespace Firebase\Auth\ListUsersPage;


use Firebase\Auth\ExportedUserRecord;
use Firebase\Auth\FirebaseUserManager;
use Firebase\Auth\ListUsersPage;

final class DefaultUserSource implements UserSource
{
    /**
     * @var FirebaseUserManager
     */
    private $userManager;

    public function __construct(FirebaseUserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(int $maxResults, ?string $pageToken): ListUsersResult
    {
        $response = $this->userManager->listUsers($maxResults, $pageToken);
        $usersBuilder = [];
        if ($response->hasUsers()) {
            foreach ($response->getUsers() as $user) {
                $usersBuilder[] = new ExportedUserRecord($user);
            }
        }

        $nextPageToken = !is_null($response->getPageToken()) ? $response->getPageToken() : ListUsersPage::END_OF_LIST;
        return new ListUsersResult($usersBuilder, $nextPageToken);
    }

}
