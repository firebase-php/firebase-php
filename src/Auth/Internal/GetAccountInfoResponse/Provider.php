<?php


namespace Firebase\Auth\Internal\GetAccountInfoResponse;


use Firebase\Auth\Internal\ResponseBuilder;

class Provider implements ResponseBuilder
{
    /**
     * @var string|null
     * @key rawId
     */
    private $uid;

    /**
     * @var string|null
     */
    private $displayName;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $phoneNumber;

    /**
     * @var string|null
     */
    private $photoUrl;

    /**
     * @var string|null
     */
    private $providerId;

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

    public static function build(array $content = null) {
        if(empty($content)) {
            return null;
        }
        $provider = new static();
        $provider->uid = $content['rawId'];
        $provider->email = $content['email'];
        $provider->phoneNumber = $content['displayName'];
        $provider->photoUrl = $content['photoUrl'];
        $provider->providerId = $content['providerId'];

        return $provider;
    }
}
