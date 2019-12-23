<?php


namespace Firebase\Auth;

use Firebase\Util\Validator\Validator;

final class UserImportOptions
{
    /**
     * @var UserImportHash
     */
    private $hash;

    public function __construct(UserImportOptionsBuilder $builder)
    {
        Validator::isNonNullObject($builder->getHash());
        $this->hash = $builder->getHash();
    }

    public static function withHash(UserImportHash $hash) {
        Validator::isNonNullObject($hash);
        return self::builder()->setHash($hash)->build();
    }

    public static function builder() {
        return new UserImportOptionsBuilder();
    }

    /**
     * @return UserImportHash
     */
    public function getHash(): UserImportHash
    {
        return $this->hash;
    }

    public function getProperties() {
        return $this->hash->getProperties();
    }
}
