<?php


namespace Firebase\Auth\Internal;

use Firebase\Auth\Internal\DownloadAccountResponse\User;

class DownloadAccountResponse implements ResponseBuilder
{
    /**
     * @var User[]
     */
    private $users = [];

    /**
     * @var string
     * @key nextPageToken
     */
    private $pageToken;

    /**
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return string
     */
    public function getPageToken(): ?string
    {
        return $this->pageToken;
    }

    public function hasUsers()
    {
        return !empty($this->users);
    }

    public static function build(array $content = null)
    {
        $response = new static();
        if (!is_array($content) || !isset($content['users'])) {
            return $response;
        }
        if (is_array($content['users'])) {
            foreach ($content['users'] as $user) {
                $response->users[] = User::build($user);
            }
        }
        $response->pageToken = $content['nextPageToken'] ?? null;
        return $response;
    }
}
