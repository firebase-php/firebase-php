<?php


namespace Firebase\Auth\UserProvider;


use Firebase\Auth\UserProvider;

class Builder
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
     * @return Builder
     */
    public function setUid(string $uid): Builder
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @param string $displayName
     * @return Builder
     */
    public function setDisplayName(string $displayName): Builder
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @param string $email
     * @return Builder
     */
    public function setEmail(string $email): Builder
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string $photoUrl
     * @return Builder
     */
    public function setPhotoUrl(string $photoUrl): Builder
    {
        $this->photoUrl = $photoUrl;
        return $this;
    }

    /**
     * @param string $providerId
     * @return Builder
     */
    public function setProviderId(string $providerId): Builder
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
