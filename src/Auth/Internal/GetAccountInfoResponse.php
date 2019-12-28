<?php


namespace Firebase\Auth\Internal;


use Firebase\Auth\Internal\GetAccountInfoResponse\User;

final class GetAccountInfoResponse implements ResponseBuilder
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

    public static function build(array $content = null)
    {
        if(empty($content)) {
            return null;
        }
        $response = new GetAccountInfoResponse();
        $response->kind = $content['kind'];
        $response->users = [];
        if(isset($content['users'])) {
            foreach($content['users'] as $user) {
                $response->users[] = User::build($user);
            }
        }

        return $response;
    }
}
