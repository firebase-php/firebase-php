<?php

namespace Firebase\Tests\Auth;

use Carbon\CarbonInterval;
use Firebase\Auth\ActionCodeSettings;
use Firebase\Auth\FirebaseAuth;
use Firebase\Auth\FirebaseAuthException;
use Firebase\Auth\FirebaseUserManager;
use Firebase\Auth\ImportUserRecord;
use Firebase\Auth\ImportUserRecordBuilder;
use Firebase\Auth\Internal\EmailLinkType;
use Firebase\Auth\SessionCookieOptions;
use Firebase\Auth\UserImportOptions;
use Firebase\Auth\UserRecord;
use Firebase\FirebaseApp;
use Firebase\FirebaseOptions;
use Firebase\Tests\Testing\MockCredentials;
use Firebase\Tests\Testing\TestOnlyImplFirebaseTrampolines;
use Firebase\Tests\Testing\TestUtils;
use FirebaseHash\Hashable;
use Google\Auth\CredentialsLoader;
use Google\Auth\Middleware\AuthTokenMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;

class FirebaseUserManagerTest extends TestCase
{
    private const TEST_TOKEN = 'token';

    private static function credentials()
    {
        return new MockCredentials(self::TEST_TOKEN);
    }

    private static function ACTION_CODE_SETTINGS()
    {
        return ActionCodeSettings::builder()
            ->setUrl('https://example.dynamic.link')
            ->setHandleCodeInApp(true)
            ->setDynamicLinkDomain('custom.page.link')
            ->setIosBundleId('com.example.ios')
            ->setAndroidPackageName('com.example.android')
            ->setAndroidInstallApp(true)
            ->setAndroidMinimumVersion('6')
            ->build();
    }

    private static function ACTION_CODE_SETTINGS_MAP()
    {
        return self::ACTION_CODE_SETTINGS()->getProperties();
    }

    protected function tearDown(): void
    {
        TestOnlyImplFirebaseTrampolines::clearInstancesForTest();
    }

    public function testGetUser()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/getUser.json'))
        ]);
        $userRecord = FirebaseAuth::getInstance()->getUser('testuser');
        self::checkUserRecord($userRecord);
    }

    public function testGetUserWithNotFoundError()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/getUserError.json'))
        ]);
        try {
            FirebaseAuth::getInstance()->getUser('testuser');
            self::fail('No error thrown for invalid response');
        } catch (\Exception $e) {
            self::assertTrue($e instanceof FirebaseAuthException);
            self::assertEquals(FirebaseUserManager::USER_NOT_FOUND_ERROR, $e->getCode());
        }
    }

    public function testGetUserByEmail()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/getUser.json'))
        ]);
        $userRecord = FirebaseAuth::getInstance()->getUserByEmail('testuser@example.com');
        self::checkUserRecord($userRecord);
    }

    public function testGetUserByEmailWithNotFoundError()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/getUserError.json'))
        ]);
        try {
            FirebaseAuth::getInstance()->getUser('testuser@example.com');
            self::fail('No error thrown for invalid response');
        } catch (\Exception $e) {
            self::assertTrue($e instanceof FirebaseAuthException);
            self::assertEquals(FirebaseUserManager::USER_NOT_FOUND_ERROR, $e->getCode());
        }
    }

    public function testGetUserByPhoneNumber()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/getUser.json'))
        ]);
        $userRecord = FirebaseAuth::getInstance()->getUserByPhoneNumber('+1234567890');
        self::checkUserRecord($userRecord);
    }

    public function testGetUserByPhoneNumberWithNotFoundError()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/getUserError.json'))
        ]);
        try {
            FirebaseAuth::getInstance()->getUserByPhoneNumber('testuser@example.com');
            self::fail('No error thrown for invalid response');
        } catch (\Exception $e) {
            self::assertTrue($e instanceof FirebaseAuthException);
            self::assertEquals(FirebaseUserManager::USER_NOT_FOUND_ERROR, $e->getCode());
        }
    }

    public function testListUsers()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/listUsers.json'))
        ]);
        $page = FirebaseAuth::getInstance()->listUsers(null, 999);
        self::assertEquals(2, count($page->getValues()));
        foreach ($page->getValues() as $userRecord) {
            self::checkUserRecord($userRecord);
            self::assertEquals('passwordHash', $userRecord->getPasswordHash());
            self::assertEquals('passwordSalt', $userRecord->getPasswordSalt());
        }

        self::assertEquals('', $page->getNextPageToken());
    }

    public function testListUsersWithPageToken()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/listUsers.json'))
        ]);
        $page = FirebaseAuth::getInstance()->listUsers('token', 999);
        self::assertEquals(2, count($page->getValues()));
        foreach ($page->getValues() as $userRecord) {
            self::checkUserRecord($userRecord);
            self::assertEquals('passwordHash', $userRecord->getPasswordHash());
            self::assertEquals('passwordSalt', $userRecord->getPasswordSalt());
        }

        self::assertEquals('', $page->getNextPageToken());
    }

    public function testListZeroUsers()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], '{}'),
        ]);
        $page = FirebaseAuth::getInstance()->listUsers(null);
        self::assertTrue(empty($page->getValues()));
        self::assertEquals('', $page->getNextPageToken());
    }

    private static function initializeAppForUserManagement(array $mockResponse = [])
    {
        $mockHandler = new MockHandler($mockResponse);
        $credentials = self::credentials();
        $middleware = new AuthTokenMiddleware($credentials);
        $stack = HandlerStack::create($mockHandler);
        $stack->push($middleware);
        $httpClient = new Client(['handler' => $stack]);
        FirebaseApp::initializeApp(
            FirebaseOptions::builder()
                ->setCredentials(self::credentials())
                ->setHttpClient($httpClient)
                ->setProjectId('test-project-id')
                ->build()
        );
    }

    private static function checkUserRecord(UserRecord $userRecord)
    {
        self::assertEquals('testuser', $userRecord->getUid());
        self::assertEquals('testuser@example.com', $userRecord->getEmail());
        self::assertEquals('+1234567890', $userRecord->getPhoneNumber());
        self::assertEquals('Test User', $userRecord->getDisplayName());
        self::assertEquals('http://www.example.com/testuser/photo.png', $userRecord->getPhotoUrl());
        self::assertEquals(1234567890, $userRecord->getUserMetadata()->getCreationTimestamp());
        self::assertEquals(0, $userRecord->getUserMetadata()->getLastSignInTimestamp());
        self::assertEquals(2, count($userRecord->getProviderData()));
        self::assertFalse($userRecord->isDisabled());
        self::assertTrue($userRecord->isEmailVerified());
        self::assertEquals(1494364393000, $userRecord->getTokensValidAfterTimestamp());

        $provider = $userRecord->getProviderData()[0];
        self::assertEquals('testuser@example.com', $provider->getUid());
        self::assertEquals('testuser@example.com', $provider->getEmail());
        self::assertNull($provider->getPhoneNumber());
        self::assertEquals('Test User', $provider->getDisplayName());
        self::assertEquals('http://www.example.com/testuser/photo.png', $provider->getPhotoUrl());
        self::assertEquals('password', $provider->getProviderId());

        $provider = $userRecord->getProviderData()[1];
        self::assertEquals('+1234567890', $provider->getUid());
        self::assertNull($provider->getEmail());
        self::assertEquals('+1234567890', $provider->getPhoneNumber());
        self::assertEquals('phone', $provider->getProviderId());

        $claims = $userRecord->getCustomClaims();
        self::assertEquals(2, count($claims));
        self::assertTrue((bool)$claims['admin']);
        self::assertEquals('gold', $claims['package']);
    }
}


final class MockHash implements Hashable
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getOptions()
    {
        return [
            'key1' => 'value1',
            'key2' => true
        ];
    }

    public function getName()
    {
        return $this->name;
    }
}
