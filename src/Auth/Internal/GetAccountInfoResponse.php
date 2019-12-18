<?php


namespace Firebase\Auth\Internal;


use Firebase\Auth\Internal\GetAccountInfoResponse\User;

final class GetAccountInfoResponse
{
    /**
     * @var string
     */
    private $kind;

    /**
     * @var User[]
     */
    private $users;

    /**
     * @return string
     */
    public function getKind(): string
    {
        return $this->kind;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }
}
