<?php


namespace Firebase\Tests\Testing;


use InvalidArgumentException;
use Lcobucci\JWT\Signature;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;

class IncorrectAlgorithmSigner extends Signer\OpenSSL
{
    const INCORRECT_ALGORITHM = 'HSA';

    public function getKeyType()
    {
        return -1;
    }

    public function getAlgorithm()
    {
        return -1;
    }

    public function getAlgorithmId()
    {
        return self::INCORRECT_ALGORITHM;
    }
}
