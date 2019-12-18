<?php


namespace Firebase\Auth;

use Firebase\Auth\UserImportOptions\Builder;
use Respect\Validation\Validator as v;

final class UserImportOptions
{
    /**
     * @var UserImportHash
     */
    private $hash;

    public function __construct(Builder $builder)
    {
        v::objectType()->notEmpty()->assert($builder->getHash());
        $this->hash = $builder->getHash();
    }

    public static function withHash(UserImportHash $hash) {
        return self::builder()->setHash($hash)->build();
    }

    public static function builder() {
        return new Builder();
    }

    /**
     * @return UserImportHash
     */
    public function getHash(): UserImportHash
    {
        return $this->hash;
    }
}
