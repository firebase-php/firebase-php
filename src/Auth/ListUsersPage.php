<?php


namespace Firebase\Auth;


use Firebase\Auth\ListUsersPage\ListUsersResult;
use Firebase\Auth\ListUsersPage\Page;
use Firebase\Auth\ListUsersPage\PageFactory;
use Firebase\Auth\ListUsersPage\UserSource;

class ListUsersPage implements Page
{
    const END_OF_LIST = '';

    /**
     * @var ListUsersResult
     */
    private $currentBatch;

    /**
     * @var UserSource
     */
    private $source;

    /**
     * @var int
     */
    private $maxResults;

    public function __construct(ListUsersResult $currentBatch, UserSource $source, int $maxResults)
    {
        $this->currentBatch = $currentBatch;
        $this->source = $source;
        $this->maxResults = $maxResults;
    }

    public function hasNextPage(): bool
    {
        return self::END_OF_LIST !== $this->currentBatch->getNextPageToken();
    }

    public function getNextPageToken(): ?string
    {
        return $this->currentBatch->getNextPageToken();
    }

    public function getNextPage(): ?ListUsersPage
    {
        if (!$this->hasNextPage()) {
            return null;
        }

        $factory = new PageFactory($this->source, $this->maxResults, $this->currentBatch->getNextPageToken());
        try {
            return $factory->create();
        } catch (FirebaseAuthException $e) {
            throw new \RuntimeException('', null, $e);
        }
    }

    public function getValues()
    {
        return $this->currentBatch->getUsers();
    }
}
