<?php


namespace Firebase\Auth;


use Firebase\Auth\UserProvider\Builder;
use Respect\Validation\Validator as v;

class UserProvider
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

    public function __construct(Builder $builder)
    {
        $v = v::stringType()->notEmpty();
        $v->assert($builder->getUid());
        $v->assert($builder->getProviderId());
        $this->uid = $builder->getUid();
        $this->displayName = $builder->getDisplayName();
        $this->email = $builder->getEmail();
        $this->photoUrl = $builder->getPhotoUrl();
        $this->providerId = $builder->getProviderId();
    }

    /**
     * @return Builder
     */
    public static function builder() {
        return new Builder();
    }
}
