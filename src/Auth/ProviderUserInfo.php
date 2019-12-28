<?php


namespace Firebase\Auth;

use Firebase\Auth\Internal\GetAccountInfoResponse\Provider;

class ProviderUserInfo implements UserInfo
{
    private $uid;

    private $displayName;

    private $email;

    private $phoneNumber;

    private $photoUrl;

    private $providerId;

    public function __construct(Provider $response)
    {
        $this->uid = $response->getUid();
        $this->displayName = $response->getDisplayName();
        $this->email = $response->getEmail();
        $this->phoneNumber = $response->getPhoneNumber();
        $this->photoUrl = $response->getPhotoUrl();
        $this->providerId = $response->getProviderId();
    }

    /**
     * @return string|null
     */
    public function getUid(): ?string
    {
        return $this->uid;
    }

    /**
     * @return string|null
     */
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @return string|null
     */
    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    /**
     * @return string|null
     */
    public function getProviderId(): ?string
    {
        return $this->providerId;
    }
}
