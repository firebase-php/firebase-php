<?php


namespace Firebase\Auth\Internal\GetAccountInfoResponse;


class User
{
    /**
     * @var string
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
     */
    private $customClaims;

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @return bool
     */
    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return string
     */
    public function getPhotoUrl(): string
    {
        return $this->photoUrl;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @return Provider[]
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getLastLoginAt(): int
    {
        return $this->lastLoginAt;
    }

    /**
     * @return int
     */
    public function getValidSince(): int
    {
        return $this->validSince;
    }

    /**
     * @return string
     */
    public function getCustomClaims(): string
    {
        return $this->customClaims;
    }

}