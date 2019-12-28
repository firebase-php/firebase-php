<?php


namespace Firebase\Auth\Internal\GetAccountInfoResponse;

use Firebase\Auth\Internal\ResponseBuilder;

class User implements ResponseBuilder
{
    /**
     * @var string
     * @key localId
     */
    private $uid;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var bool
     */
    private $emailVerified;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var string
     */
    private $photoUrl;

    /**
     * @var bool
     */
    private $disabled;

    /**
     * @var Provider[]
     * @key providerUserInfo
     */
    private $providers;

    /**
     * @var int
     */
    private $createdAt;

    /**
     * @var int
     */
    private $lastLoginAt;

    /**
     * @var int
     */
    private $validSince;

    /**
     * @var string
     * @key customAttributes
     */
    private $customClaims;

    /**
     * @return string
     */
    public function getUid(): ?string
    {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @return bool
     */
    public function isEmailVerified(): ?bool
    {
        return $this->emailVerified;
    }

    /**
     * @return string
     */
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * @return string
     */
    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    /**
     * @return bool
     */
    public function isDisabled(): ?bool
    {
        return $this->disabled;
    }

    /**
     * @return Provider[]
     */
    public function getProviders(): ?array
    {
        return $this->providers;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getLastLoginAt(): ?int
    {
        return $this->lastLoginAt;
    }

    /**
     * @return int
     */
    public function getValidSince(): ?int
    {
        return $this->validSince;
    }

    /**
     * @return string
     */
    public function getCustomClaims(): ?string
    {
        return $this->customClaims;
    }

    /**
     * @param array $content
     * @return static
     */
    public static function build(array $content = null)
    {
        if (empty($content)) {
            return null;
        }
        $user = new static();
        $user->uid = $content['localId'] ?? null;
        $user->email = $content['email'] ?? null;
        $user->phoneNumber = $content['phoneNumber'] ?? null;
        $user->emailVerified = $content['emailVerified'] ?? false;
        $user->displayName = $content['displayName'] ?? null;
        $user->photoUrl = $content['photoUrl'] ?? null;
        $user->disabled = $content['disabled'] ?? false;
        $user->providers = [];
        $providers = $content['providerUserInfo'] ?? [];
        if (is_array($providers)) {
            foreach ($providers as $provider) {
                $user->providers[] = Provider::build($provider);
            }
        }
        $user->createdAt = $content['createdAt'] ?? null;
        $user->lastLoginAt = $content['lastLoginAt'] ?? null;
        $user->validSince = $content['validSince'] ?? null;
        $user->customClaims = $content['customAttributes'] ?? null;
        return $user;
    }
}
