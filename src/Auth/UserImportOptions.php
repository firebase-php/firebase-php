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

    /**
     * @var string
     */
    private $secretKey;

    public function __construct(UserImportOptionsBuilder $builder)
    {
        Validator::isNonNullObject($builder->getHash());
        $this->hash = $builder->getHash();
        $this->secretKey = $builder->getSecretKey();
    }

    public static function withHash(Signer $hash, string $secretKey)
    {
        return self::builder()
            ->setHash($hash)
            ->setSecretKey($secretKey)
            ->build();
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

    /**
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function getProperties()
    {
        return [
            'hashAlgorithm' => $this->hash->getAlgorithmId(),
            'signerKey' => base64_encode($this->secretKey)
        ];
    }
}
