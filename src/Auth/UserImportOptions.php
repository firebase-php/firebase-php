<?php


namespace Firebase\Auth;

use Firebase\Util\Validator\Validator;
use Lcobucci\JWT\Signer;

final class UserImportOptions
{
    /**
     * @var Signer
     */
    private $hash;

    public function __construct(UserImportOptionsBuilder $builder)
    {
        Validator::isNonNullObject($builder->getHash());
        $this->hash = $builder->getHash();
    }

    public static function withHash(?Signer $hash = null)
    {
        Validator::isNonNullObject($hash);
        return self::builder()->setHash($hash)->build();
    }

    public static function builder()
    {
        return new UserImportOptionsBuilder();
    }

    /**
     * @return Signer
     */
    public function getHash(): ?Signer
    {
        return $this->hash;
    }

    public function getProperties()
    {
        return [
            'hashAlgorithm' => $this->hash->getAlgorithmId(),
            'signerKey' => base64_encode($this->hash->getAlgorithmId())
        ];
    }
}
