<?php


namespace Firebase\Auth;

use Firebase\Util\Validator\Validator;

final class UserProvider implements \JsonSerializable
{
    /**
     * @var string
     * @key rawId
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

    public static function builder()
    {
        return new UserProviderBuilder();
    }

    public function jsonSerialize()
    {
        $json = [
            'rawId' => $this->uid,
            'providerId' => $this->providerId,
        ];
        if (!empty($this->email)) {
            $json['email'] = $this->email;
        }
        if (!empty($this->photoUrl)) {
            $json['photoUrl'] = $this->photoUrl;
        }
        if (!empty($this->displayName)) {
            $json['displayName'] = $this->displayName;
        }
        return $json;
    }
}
