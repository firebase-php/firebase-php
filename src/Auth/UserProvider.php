<?php


namespace Firebase\Auth;


use Firebase\Auth\UserProvider\UserProviderBuilder;
use Firebase\Util\Validator\Validator;

final class UserProvider
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

    public function __construct(UserProviderBuilder $builder)
    {
        Validator::isNonEmptyString($builder->getUid());
        Validator::isNonEmptyString($builder->getProviderId());
        $this->uid = $builder->getUid();
        $this->displayName = $builder->getDisplayName();
        $this->email = $builder->getEmail();
        $this->photoUrl = $builder->getPhotoUrl();
        $this->providerId = $builder->getProviderId();
    }

    public static function builder() {
        return new UserProviderBuilder();
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
     * @param string $displayName
     * @return UserProvider
     */
    public function setDisplayName(string $displayName): UserProvider
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return UserProvider
     */
    public function setEmail(string $email): UserProvider
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhotoUrl(): string
    {
        return $this->photoUrl;
    }

    /**
     * @param string $photoUrl
     * @return UserProvider
     */
    public function setPhotoUrl(string $photoUrl): UserProvider
    {
        $this->photoUrl = $photoUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getProviderId(): string
    {
        return $this->providerId;
    }
}
