<?php

namespace Firebase\Tests\Auth;

use Firebase\Auth\UserImportOptions;
use FirebaseHash\HmacSha512;
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
        $hash = HmacSha512::builder()
            ->setKey('key')
            ->build();
        $options = UserImportOptions::builder()
            ->setHash($hash)
            ->build();
        $expected = [
            'hashAlgorithm' => 'HMAC_SHA512',
            'signerKey' => 'key'
        ];
        $this->assertEquals($expected, $options->getProperties());
        $this->assertSame($hash, $options->getHash());
    }
}
