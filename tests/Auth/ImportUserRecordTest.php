<?php

namespace Firebase\Tests\Auth;

use Carbon\Carbon;
use Firebase\Auth\FirebaseUserManager;
use Firebase\Auth\ImportUserRecord;
use Firebase\Auth\UserMetadata;
use Firebase\Auth\UserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class ImportUserRecordTest extends TestCase
{
    public function testUidOnlyRecord()
    {
        $record = ImportUserRecord::builder()
            ->setUid('testuid')
            ->build();
        $this->assertEquals(['localId' => 'testuid'], $record->getProperties());
    }

    public function testAllProperties()
    {
        $date = Carbon::now();

        $provider1 = UserProvider::builder()
            ->setUid('testuid')
            ->setProviderId('google.com')
            ->build();
        $provider2 = UserProvider::builder()
            ->setUid('testuid')
            ->setProviderId('test.com')
            ->build();
        $builder = ImportUserRecord::builder();
        $builder->setUid('testuid')
            ->setEmail('test@example.com')
            ->setDisplayName('Test User')
            ->setPhotoUrl('https://test.com/user.png')
            ->setPhoneNumber('+123456789')
            ->setUserMetadata(new UserMetadata($date->timestamp, $date->timestamp))
            ->setDisabled(false)
            ->setEmailVerified(true)
            ->setPasswordHash('password')
            ->setPasswordSalt('salt')
            ->addUserProvider($provider1)
            ->addAllUserProviders([$provider2])
            ->putCustomClaim('admin', true)
            ->putAllCustomClaims(['package' => 'gold']);
        $record = $builder->build();
        $properties = $record->getProperties();
        $customAttributes = $properties['customAttributes'] ?? '';
        $customClaims = json_decode($customAttributes, true);
        $this->assertEquals(
            ['admin' => true, 'package' => 'gold'],
            $customClaims
        );
        $expected = [
            'localId' => 'testuid',
            'email' => 'test@example.com',
            'displayName' => 'Test User',
            'photoUrl' => 'https://test.com/user.png',
            'phoneNumber' => '+123456789',
            'createdAt' => $date->timestamp,
            'lastLoginAt' => $date->timestamp,
            'disabled' => false,
            'emailVerified' => true,
            'passwordHash' => base64_encode('password'),
            'salt' => base64_encode('salt'),
            'providerUserInfo' => [$provider1, $provider2],
            'customAttributes' => $customAttributes
        ];

        $this->assertEquals($expected, $properties);
    }

    public function testInvalidUid()
    {
        $this->expectException(InvalidArgumentException::class);
        ImportUserRecord::builder()
            ->setUid(str_repeat('a', 129))
            ->build();
    }

    public function testInvalidEmail()
    {
        $this->expectException(InvalidArgumentException::class);
        ImportUserRecord::builder()
            ->setUid('test')
            ->setEmail('not-a-valid-email')
            ->build();
    }

    public function testInvalidPhotoUrl()
    {
        $this->expectException(InvalidArgumentException::class);
        ImportUserRecord::builder()
            ->setUid('test')
            ->setPhotoUrl('not-a-valid-url')
            ->build();
    }

    public function testInvalidPhoneNumber()
    {
        $this->expectException(InvalidArgumentException::class);
        ImportUserRecord::builder()
            ->setUid('test')
            ->setPhoneNumber('not a phone number')
            ->build();
    }

    public function testNullUserProvider()
    {
        try {
            ImportUserRecord::builder()
                ->setUid('test')
                ->addUserProvider(null)
                ->build();
            $this->fail('No error thrown for null provider');
        } catch (\Exception $e) {
            // expected;
        }
        try {
            $providers = [null];
            ImportUserRecord::builder()
                ->setUid('test')
                ->addAllUserProviders($providers)
                ->build();
            $this->fail('No error thrown for null provider');
        } catch (\Exception $e) {
            // expected;
        }
    }

    public function testNullOrEmptyCustomClaims()
    {
        try {
            ImportUserRecord::builder()
                ->setUid('test')
                ->putCustomClaim('foo', null)
                ->build();
            $this->fail('No error thrown for null claim value');
        } catch (\Exception $e) {
            // expected;
        }
        try {
            ImportUserRecord::builder()
                ->setUid('test')
                ->putCustomClaim(null, 'foo')
                ->build();
            $this->fail('No error thrown for null claim name');
        } catch (\Exception $e) {
            // expected;
        }
        try {
            ImportUserRecord::builder()
                ->setUid('test')
                ->putCustomClaim('', 'foo')
                ->build();
            $this->fail('No error thrown for empty claim name');
        } catch (\Exception $e) {
            // expected;
        }
    }

    public function testReservedClaims()
    {
        $this->expectNotToPerformAssertions();
        foreach (FirebaseUserManager::RESERVED_CLAIMS as $key) {
            try {
                ImportUserRecord::builder()
                    ->setUid('test')
                    ->putCustomClaim($key, 'foo')
                    ->build();
                $this->fail('No error thrown for reserved claim');
            } catch (\Exception $e) {
                // expected
            }
        }
    }

    public function testLargeCustomClaims()
    {
        $this->expectNotToPerformAssertions();
        $record = ImportUserRecord::builder()
            ->setUid('test')
            ->putCustomClaim('foo', str_repeat('a', 1000))
            ->build();
        try {
            $record->getProperties();
            $this->fail('No error thrown for large claim value');
        } catch (\Exception $e) {
        }
    }
}
