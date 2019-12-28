<?php

namespace Firebase\Tests\Auth;

use Firebase\Auth\FirebaseToken;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class FirebaseTokenTest extends TestCase
{
    public function testFirebaseToken()
    {
        $claims = [
            'sub' => 'testUser',
            'iss' => 'test-project-id',
            'email' => 'test@example.com',
            'email_verified' => true,
            'name' => 'Test User',
            'picture' => 'https://picture.url',
            'custom' => 'claim',
        ];
        $token = new FirebaseToken($claims);
        self::assertEquals('testUser', $token->getUid());
        self::assertEquals('test-project-id', $token->getIssuer());
        self::assertEquals('test@example.com', $token->getEmail());
        self::assertEquals('testUser', $token->getUid());
        self::assertTrue($token->isEmailVerified());
        self::assertEquals('https://picture.url', $token->getPicture());
        self::assertEquals('claim', $token->getClaims()['custom']);
        self::assertEquals(7, count($token->getClaims()));
    }

    public function testFirebaseTokenMinimal()
    {
        $claims = [
            'sub' => 'testUser'
        ];
        $token = new FirebaseToken($claims);

        self::assertEquals('testUser', $token->getUid());
        self::assertNull($token->getIssuer());
        self::assertNull($token->getEmail());
        self::assertFalse($token->isEmailVerified());
        self::assertNull($token->getName());
        self::assertNull($token->getPicture());
        self::assertEquals(1, count($token->getClaims()));
    }

    public function testFirebaseTokenNullClaims()
    {
        $this->expectException(InvalidArgumentException::class);
        new FirebaseToken(null);
    }

    public function testFirebaseTokenNoUid()
    {
        $claims = [
            'custom' => 'claim'
        ];
        $this->expectException(InvalidArgumentException::class);
        new FirebaseToken($claims);
    }
}
