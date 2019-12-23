<?php


namespace Firebase\Auth;


use Firebase\Util\Validator\Validator;

final class FirebaseToken
{
    /**
     * @var array
     */
    private $claims;

    public function __construct(array $claims = null)
    {
        Validator::checkArgument(is_array($claims) && in_array('sub', array_keys($claims)));
        $this->claims = array_replace([], $claims);
    }

    public function getUid(): string {
        return $this->claims['sub'];
    }

    public function getIssuer(): string {
        return $this->claims['iss'];
    }

    public function getName(): string {
        return $this->claims['name'];
    }

    public function getPicture(): string {
        return $this->claims['picture'];
    }

    public function getEmail(): string {
        return $this->claims['email'];
    }

    public function isEmailVerified(): bool {
        return !!$this->claims['email_verified'];
    }

    public function getClaims(): array {
        return $this->claims;
    }
}
