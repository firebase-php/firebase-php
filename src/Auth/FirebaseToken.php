<?php


namespace Firebase\Auth;


use Firebase\Util\Validator\Validator;

final class FirebaseToken
{
    /**
     * @var array
     */
    private $claims;

    public function __construct(?array $claims = null)
    {
        // TODO: allow JWT Token as claims parameter
        Validator::checkArgument(is_array($claims) && isset($claims['sub']), 'Claims map must at least contain sub');
        $this->claims = array_replace([], $claims);
    }

    public function getUid(): ?string {
        return $this->claims['sub'] ?? null;
    }

    public function getIssuer(): ?string {
        return $this->claims['iss'] ?? null;
    }

    public function getName(): ?string {
        return $this->claims['name'] ?? null;
    }

    public function getPicture(): ?string {
        return $this->claims['picture'] ?? null;
    }

    public function getEmail(): ?string {
        return $this->claims['email'] ?? null;
    }

    public function isEmailVerified(): ?bool {
        return $this->claims['email_verified'] ?? false;
    }

    public function getClaims(): ?array {
        return $this->claims;
    }
}
