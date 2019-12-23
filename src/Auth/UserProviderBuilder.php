<?php


namespace Firebase\Auth\UserProvider;


use Firebase\Auth\UserProvider;

final class UserProviderBuilder
{
    /**
     * @var string
     */
    private $uid;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $photoUrl;

    /**
     * @var string
     */
    private $providerId;

    /**
     * @param string $uid
     * @return UserProviderBuilder
     */
    public function setUid(string $uid): UserProviderBuilder
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @param string $displayName
     * @return UserProviderBuilder
     */
    public function setDisplayName(string $displayName): UserProviderBuilder
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @param string $email
     * @return UserProviderBuilder
     */
    public function setEmail(string $email): UserProviderBuilder
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string $photoUrl
     * @return UserProviderBuilder
     */
    public function setPhotoUrl(string $photoUrl): UserProviderBuilder
    {
        $this->photoUrl = $photoUrl;
        return $this;
    }

    /**
     * @param string $providerId
     * @return UserProviderBuilder
     */
    public function setProviderId(string $providerId): UserProviderBuilder
    {
        $this->providerId = $providerId;
        return $this;
    }

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
    public function getDisplayName(): string
    {
        return $this->displayName;
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
    public function getPhotoUrl(): string
    {
        return $this->photoUrl;
    }

    /**
     * @return string
     */
    public function getProviderId(): string
    {
        return $this->providerId;
    }

    /**
     * @return UserProvider
     */
    public function build() {
        return new UserProvider($this);
    }
}
