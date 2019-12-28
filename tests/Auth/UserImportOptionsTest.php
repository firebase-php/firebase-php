<?php

namespace Firebase\Tests\Auth;

use Firebase\Auth\UserImportOptions;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class UserImportOptionsTest extends TestCase
{
    public function testEmptyOptions()
    {
        $this->expectException(InvalidArgumentException::class);
        UserImportOptions::builder()->build();
    }

    public function testHash()
    {
        $hash = new Sha512();
        $options = UserImportOptions::builder()
            ->setHash($hash)
            ->build();
        $expected = [
            'hashAlgorithm' => 'HS512',
            'signerKey' => base64_encode('HS512')
        ];
        $this->assertEquals($expected, $options->getProperties());
        $this->assertSame($hash, $options->getHash());
    }
}
