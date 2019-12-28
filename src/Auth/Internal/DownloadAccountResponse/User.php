<?php


namespace Firebase\Auth\Internal\DownloadAccountResponse;

use Firebase\Auth\Internal\GetAccountInfoResponse;

class User extends GetAccountInfoResponse\User
{
    /**
     * @var string
     */
    private $passwordHash;

    /**
     * @var string
     * @key salt
     */
    private $passwordSalt;

    /**
     * @param array $content
     * @return User|GetAccountInfoResponse\User
     */
    public static function build(array $content = null)
    {
        if (empty($content)) {
            return null;
        }
        /** @var User $user */
        $user = parent::build($content);
        $user->passwordSalt = $content['salt'] ?? null;
        $user->passwordHash = $content['passwordHash'] ?? null;

        return $user;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * @return string
     */
    public function getPasswordSalt(): ?string
    {
        return $this->passwordSalt;
    }
}
