<?php


namespace Firebase\Auth;

use Firebase\Util\Validator\Validator;
use FirebaseHash\Hashable;

final class UserImportOptions
{
    /**
     * @var Hashable
     */
    private $hash;

    public function __construct(UserImportOptionsBuilder $builder)
    {
        $this->hash = Validator::isNonNullObject($builder->getHash());
    }

    public static function withHash(Hashable $hash)
    {
        return self::builder()
            ->setHash($hash)
            ->build();
    }

    public static function builder()
    {
        return new UserImportOptionsBuilder();
    }

    /**
     * @return Hashable
     */
    public function getHash(): Hashable
    {
        return $this->hash;
    }

    public function getProperties()
    {
        $properties = $this->hash->getOptions();
        return array_merge($properties, [
            'hashAlgorithm' => $this->hash->getName()
        ]);
    }
}
