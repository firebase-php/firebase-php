<?php


namespace Firebase\Tests\Auth;

use Firebase\Auth\UserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class UserProviderTest extends TestCase
{
    public function testAllProperties()
    {
        $provider = UserProvider::builder()
            ->setUid('testuid')
            ->setProviderId('google.com')
            ->setEmail('test@example.com')
            ->setDisplayName('Test User')
            ->setPhotoUrl('https://test.com/user.png')
            ->build();
        $json = json_encode($provider);
        $parsed = json_decode($json, true);
        $expected = [
            'rawId' => 'testuid',
            'providerId' => 'google.com',
            'email' => 'test@example.com',
            'displayName' => 'Test User',
            'photoUrl' => 'https://test.com/user.png'
        ];
        $this->assertEquals($expected, $parsed);
    }

    public function testRequiredProperties()
    {
        $provider = UserProvider::builder()
            ->setUid('testuid')
            ->setProviderId('google.com')
            ->build();
        $json = json_encode($provider);
        $parsed = json_decode($json, true);
        $expected = [
            'rawId' => 'testuid',
            'providerId' => 'google.com',
        ];
        $this->assertEquals($expected, $parsed);
    }

    public function testNoUid()
    {
        $this->expectException(InvalidArgumentException::class);
        UserProvider::builder()
            ->setProviderId('google.com')
            ->build();
    }

    public function testNoProviderId()
    {
        $this->expectException(InvalidArgumentException::class);
        UserProvider::builder()
            ->setUid('testuid')
            ->build();
    }
}
