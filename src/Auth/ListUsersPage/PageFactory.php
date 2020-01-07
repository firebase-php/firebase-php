<?php


namespace Firebase\Auth\ListUsersPage;

use Firebase\Auth\FirebaseUserManager;
use Firebase\Auth\ListUsersPage;
use Firebase\Util\Validator\Validator;

final class PageFactory
{
    /**
     * @var UserSource
     */
    private $source;

    /**
     * @var int
     */
    private $maxResults;

    /**
     * @var string|null
     */
    private $pageToken;

    public function __construct(UserSource $source, int $maxResults = FirebaseUserManager::MAX_LIST_USERS_RESULT, ?string $pageToken = null)
    {
        Validator::checkArgument(
            $maxResults > 0 && $maxResults <= FirebaseUserManager::MAX_LIST_USERS_RESULT,
            sprintf(
                'maxResults must be a positive integer that does not exceed %s',
                FirebaseUserManager::MAX_LIST_USERS_RESULT
            )
        );
        Validator::checkArgument($pageToken !== ListUsersPage::END_OF_LIST, 'invalid end of list page token');
        $this->source = $source;
        $this->maxResults = $maxResults;
        $this->pageToken = $pageToken;
    }

    public function create()
    {
        $batch = $this->source->fetch($this->maxResults, $this->pageToken);
        return new ListUsersPage($batch, $this->source, $this->maxResults);
    }
}
