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

    public function testCreateUser()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/createUser.json')),
            new Response(200, [], TestUtils::loadResource('/getUser.json')),
        ]);
        $user = FirebaseAuth::getInstance()->createUser(new UserRecord\CreateRequest());
        self::checkUserRecord($user);
    }

    public function testUpdateUser()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/createUser.json')),
            new Response(200, [], TestUtils::loadResource('/getUser.json')),
        ]);
        $user = FirebaseAuth::getInstance()->updateUser(new UserRecord\UpdateRequest('testuser'));
        self::checkUserRecord($user);
    }

    public function testImportUsers()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], '{}'),
        ]);
        $user1 = ImportUserRecord::builder()->setUid('user1')->build();
        $user2 = ImportUserRecord::builder()->setUid('user2')->build();

        $users = [$user1, $user2];
        $result = FirebaseAuth::getInstance()->importUsers($users);

        self::assertEquals(2, $result->getSuccessCount());
        self::assertEquals(0, $result->getFailureCount());
        self::assertTrue(empty($result->getErrors()));
    }

    public function testImportUsersError()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/importUsersError.json')),
        ]);
        $user1 = ImportUserRecord::builder()->setUid('user1')->build();
        $user2 = ImportUserRecord::builder()->setUid('user2')->build();
        $user3 = ImportUserRecord::builder()->setUid('user3')->build();

        $users = [$user1, $user2, $user3];
        $result = FirebaseAuth::getInstance()->importUsers($users);

        self::assertEquals(1, $result->getSuccessCount());
        self::assertEquals(2, $result->getFailureCount());
        self::assertEquals(2, count($result->getErrors()));

        $error = $result->getErrors()[0];
        self::assertEquals(0, $error->getIndex());
        self::assertEquals('Some error occurred in user1', $error->getReason());
        $error = $result->getErrors()[1];
        self::assertEquals(2, $error->getIndex());
        self::assertEquals('Another error occurred in user3', $error->getReason());
    }

    public function testImportUsersWithHash()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], '{}'),
        ]);
        $user1 = ImportUserRecord::builder()
            ->setUid('user1')
            ->build();
        $user2 = ImportUserRecord::builder()
            ->setUid('user2')
            ->setPasswordHash(base64_encode('password'))
            ->build();

        $users = [$user1, $user2];
        $hash = new MockHash('MOCK_HASH');
        $result = FirebaseAuth::getInstance()
            ->importUsers($users, UserImportOptions::withHash($hash));
        self::assertEquals(2, $result->getSuccessCount());
        self::assertEquals(0, $result->getFailureCount());
        self::assertEmpty($result->getErrors());
    }

    public function testImportUsersMissingHash()
    {
        self::initializeAppForUserManagement();
        $user1 = ImportUserRecord::builder()
            ->setUid('user1')
            ->build();
        $user2 = ImportUserRecord::builder()
            ->setUid('user2')
            ->setPasswordHash(base64_encode('password'))
            ->build();

        $users = [$user1, $user2];

        try {
            FirebaseAuth::getInstance()->importUsers($users);
            self::fail('No error thrown for missing hash option');
        } catch (InvalidArgumentException $e) {
            self::assertEquals(
                'UserImportHash option is required when at least one user has a password. '
                . 'Provide a UserImportHash via UserImportOptions::withHash().',
                $e->getMessage()
            );
        }
    }

    public function testImportUsersEmptyList()
    {
        self::initializeAppForUserManagement();

        $this->expectException(InvalidArgumentException::class);
        FirebaseAuth::getInstance()->importUsers([]);
    }

    public function testImportUsersLargeList()
    {
        self::initializeAppForUserManagement();
        $users = [];
        for ($i = 0; $i < 1001; $i++) {
            $users[] = ImportUserRecord::builder()->setUid("test$i")->build();
        }

        $this->expectException(InvalidArgumentException::class);
        FirebaseAuth::getInstance()->importUsers($users);
    }

    public function testCreateSessionCookie()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/createSessionCookie.json')),
        ]);
        $options = SessionCookieOptions::builder()
            ->setExpiresIn(CarbonInterval::hours(1)->totalMilliseconds)
            ->build();
        $cookie = FirebaseAuth::getInstance()->createSessionCookie('testToken', $options);
        self::assertEquals('MockCookieString', $cookie);
    }

    public function testCreateSessionCookieInvalidArguments()
    {
        self::initializeAppForUserManagement();
        $options = SessionCookieOptions::builder()
            ->setExpiresIn(CarbonInterval::hours(1)->totalMilliseconds)
            ->build();

        try {
            FirebaseAuth::getInstance()->createSessionCookie(null, $options);
            self::fail('No error thrown for null id token');
        } catch (\TypeError $e) {
        }

        try {
            FirebaseAuth::getInstance()->createSessionCookie('', $options);
            self::fail('No error thrown for empty id token');
        } catch (InvalidArgumentException $e) {
        }

        try {
            FirebaseAuth::getInstance()->createSessionCookie('idToken', null);
            self::fail('No error thrown for null options');
        } catch (\TypeError $e) {
        }

        self::assertTrue(true);
    }

    public function testInvalidSessionCookieOptions()
    {
        try {
            SessionCookieOptions::builder()->build();
            self::fail('No error thrown for unspecified expiresIn');
        } catch (InvalidArgumentException $e) {
        }

        try {
            SessionCookieOptions::builder()
                ->setExpiresIn(CarbonInterval::seconds(299)->totalMilliseconds)
                ->build();
            self::fail('No error thrown for low expiresIn');
        } catch (InvalidArgumentException $e) {
        }

        try {
            SessionCookieOptions::builder()
                ->setExpiresIn(CarbonInterval::days(14)->totalMilliseconds + 1)
                ->build();
            self::fail('No error thrown for high expiresIn');
        } catch (InvalidArgumentException $e) {
        }

        self::assertTrue(true);
    }

    public function testGetUserHttpError()
    {
        $ops = [
            function (FirebaseAuth $auth) {
                $auth->getUserByEmail('testuser@example.com');
            },
            function (FirebaseAuth $auth) {
                $auth->getUserByPhoneNumber('+1234567890');
            },
            function (FirebaseAuth $auth) {
                $auth->createUser(new UserRecord\CreateRequest());
            },
            function (FirebaseAuth $auth) {
                $auth->updateUser(new UserRecord\UpdateRequest('test'));
            },
            function (FirebaseAuth $auth) {
                $auth->deleteUser('testuser');
            },
            function (FirebaseAuth $auth) {
                $auth->listUsers(null, 1000);
            },
        ];
        $codes = [302, 400, 401, 404, 500];
        $mockResponse = [];

        // For test common HTTP error codes
        foreach ($codes as $code) {
            foreach ($ops as $op) {
                $mockResponse[] = new Response($code, [], '{}');
            }
        }

        // For test error payload parsing
        foreach ($ops as $op) {
            $mockResponse[] = new Response(500, [], '{"error": {"message": "USER_NOT_FOUND"}}');
        }
        $mockHandler = new MockHandler($mockResponse);
        $credentials = self::credentials();
        $middleware = new AuthTokenMiddleware($credentials);
        $stack = HandlerStack::create($mockHandler);
        $stack->push($middleware);
        $httpClient = new Client(['handler' => $stack]);
        FirebaseApp::initializeApp(
            FirebaseOptions::builder()
                ->setCredentials($credentials)
                ->setProjectId('test-project-id')
                ->setHttpClient($httpClient)
                ->build()
        );

        // Test common HTTP error codes
        foreach ($codes as $code) {
            foreach ($ops as $op) {
                try {
                    $op(FirebaseAuth::getInstance());
                    self::fail('No error thrown for HTTP error: ' . $code);
                } catch (\Exception $e) {
                    self::assertTrue($e instanceof FirebaseAuthException);
                    $msg = sprintf('Unexpected HTTP response with status: %d; body: {}', $code);
                    self::assertEquals($msg, $e->getMessage());
                    self::assertEquals(FirebaseUserManager::INTERNAL_ERROR, $e->getCode());
                    self::assertTrue($e->getPrevious() instanceof BadResponseException);
                }
            }
        }

        // Test error payload parsing
        foreach ($ops as $op) {
            try {
                $op(FirebaseAuth::getInstance());
                self::fail('No error thrown for HTTP error');
            } catch (\Exception $e) {
                self::assertTrue($e instanceof FirebaseAuthException);
                self::assertEquals('User management service responded with an error', $e->getMessage());
                self::assertEquals(FirebaseUserManager::USER_NOT_FOUND_ERROR, $e->getCode());
                self::assertTrue($e->getPrevious() instanceof BadResponseException);
            }
        }
    }

    public function testGetUserMalformedJsonError()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], '{"not" json}')
        ]);
        try {
            FirebaseAuth::getInstance()->getUser('testuser');
            self::fail('No error thrown for JSON error');
        } catch (\Exception $e) {
            self::assertTrue($e instanceof FirebaseAuthException);
            self::assertTrue($e->getPrevious() instanceof \InvalidArgumentException);
            self::assertEquals(FirebaseUserManager::INTERNAL_ERROR, $e->getCode());
        }
    }

    public function testGetUserUnexpectedHttpError()
    {
        $mockHandler = new MockHandler([
            new Response(500, [], '{"not" json}')
        ]);
        $credentials = self::credentials();
        $middleware = new AuthTokenMiddleware($credentials);
        $stack = HandlerStack::create($mockHandler);
        $stack->push($middleware);
        $httpClient = new Client(['handler' => $stack]);
        FirebaseApp::initializeApp(
            FirebaseOptions::builder()
                ->setCredentials($credentials)
                ->setProjectId('test-project-id')
                ->setHttpClient($httpClient)
                ->build()
        );
        try {
            FirebaseAuth::getInstance()->getUser('testuser');
            self::fail('No error thrown for JSON error');
        } catch (\Exception $e) {
            self::assertTrue($e instanceof FirebaseAuthException);
            self::assertTrue($e->getPrevious() instanceof BadResponseException);
            self::assertEquals('Unexpected HTTP response with status: 500; body: {"not" json}', $e->getMessage());
            self::assertEquals(FirebaseUserManager::INTERNAL_ERROR, $e->getCode());
        }
    }

    public function testUserBuilder()
    {
        $map = (new UserRecord\CreateRequest())->getProperties();
        self::assertEmpty($map);
    }

    public function testUserBuilderWithParams()
    {
        $map = (new UserRecord\CreateRequest())
            ->setUid('TestUid')
            ->setDisplayName('Display Name')
            ->setPhotoUrl('http://test.com/example.png')
            ->setEmail('test@example.com')
            ->setPhoneNumber('+1234567890')
            ->setEmailVerified(true)
            ->setPassword('secret')
            ->getProperties();
        self::assertEquals(7, count($map));
        self::assertEquals('TestUid', $map['localId']);
        self::assertEquals('Display Name', $map['displayName']);
        self::assertEquals('http://test.com/example.png', $map['photoUrl']);
        self::assertEquals('test@example.com', $map['email']);
        self::assertEquals('+1234567890', $map['phoneNumber']);
        self::assertTrue((bool)$map['emailVerified']);
        self::assertEquals('secret', $map['password']);
    }

    public function testInvalidUid()
    {
        $user = new UserRecord\CreateRequest();
        try {
            $user->setUid(null);
            self::fail('No error thrown for null uid');
        } catch (\TypeError $e) {
            // expected
        }

        try {
            $user->setUid('');
            self::fail('No error thrown for empty uid');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        try {
            $user->setUid(sprintf('%0129d', 0));
            self::fail('No error thrown for long uid');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        self::assertTrue(true);
    }

    public function testInvalidDisplayName()
    {
        $user = new UserRecord\CreateRequest();
        try {
            $user->setDisplayName(null);
            self::fail('No error thrown for null uid');
        } catch (\TypeError $e) {
            // expected
        }

        self::assertTrue(true);
    }

    public function testInvalidPhotoUrl()
    {
        $user = new UserRecord\CreateRequest();
        try {
            $user->setPhotoUrl(null);
            self::fail('No error thrown for null photo url');
        } catch (\TypeError $e) {
            // expected
        }

        try {
            $user->setPhotoUrl('');
            self::fail('No error thrown for empty photo url');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        try {
            $user->setPhotoUrl('not-a-url');
            self::fail('No error thrown for invalid photo url');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        self::assertTrue(true);
    }

    public function testInvalidEmail()
    {
        $user = new UserRecord\CreateRequest();
        try {
            $user->setEmail(null);
            self::fail('No error thrown for null email');
        } catch (\TypeError $e) {
            // expected
        }

        try {
            $user->setEmail('');
            self::fail('No error thrown for empty email');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        try {
            $user->setEmail('not-a-email');
            self::fail('No error thrown for invalid email');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        self::assertTrue(true);
    }

    public function testInvalidPhoneNumber()
    {
        $user = new UserRecord\CreateRequest();
        try {
            $user->setPhoneNumber(null);
            self::fail('No error thrown for null phone number');
        } catch (\TypeError $e) {
            // expected
        }

        try {
            $user->setPhoneNumber('');
            self::fail('No error thrown for empty phone number');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        try {
            $user->setPhoneNumber('not-a-phone');
            self::fail('No error thrown for invalid phone number');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        self::assertTrue(true);
    }

    public function testInvalidPassword()
    {
        $user = new UserRecord\CreateRequest();
        try {
            $user->setPassword(null);
            self::fail('No error thrown for null password');
        } catch (\TypeError $e) {
            // expected
        }

        try {
            $user->setPassword('aaaaa');
            self::fail('No error thrown for short password');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        self::assertTrue(true);
    }

    public function testUserUpdater()
    {
        $update = new UserRecord\UpdateRequest('test');
        $claims = [
            'admin' => true,
            'package' => 'gold'
        ];
        $map = $update
            ->setDisplayName('Display Name')
            ->setPhotoUrl('http://test.com/example.png')
            ->setEmail('test@example.com')
            ->setPhoneNumber('+1234567890')
            ->setEmailVerified(true)
            ->setPassword('secret')
            ->setCustomClaims($claims)
            ->getProperties();
        self::assertEquals(8, count($map));
        self::assertEquals($update->getUid(), $map['localId']);
        self::assertEquals('Display Name', $map['displayName']);
        self::assertEquals('http://test.com/example.png', $map['photoUrl']);
        self::assertEquals('test@example.com', $map['email']);
        self::assertEquals('+1234567890', $map['phoneNumber']);
        self::assertTrue((bool)$map['emailVerified']);
        self::assertEquals('secret', $map['password']);
        self::assertEquals(json_encode($claims), $map['customAttributes']);
    }

    public function testNullCustomClaims()
    {
        $update = new UserRecord\UpdateRequest('test');
        $map = $update
            ->setCustomClaims(null)
            ->getProperties();

        self::assertEquals(2, count($map));
        self::assertEquals($update->getUid(), $map['localId']);
        self::assertEquals('{}', $map['customAttributes']);
    }

    public function testEmptyCustomClaims()
    {
        $update = new UserRecord\UpdateRequest('test');
        $map = $update
            ->setCustomClaims([])
            ->getProperties();

        self::assertEquals(2, count($map));
        self::assertEquals($update->getUid(), $map['localId']);
        self::assertEquals('{}', $map['customAttributes']);
    }

    public function testDeleteDisplayName()
    {
        $update = new UserRecord\UpdateRequest('test');
        $map = $update
            ->setDisplayName(null)
            ->getProperties();
        self::assertEquals(['DISPLAY_NAME'], $map['deleteAttribute']);
    }

    public function testDeletePhotoUrl()
    {
        $update = new UserRecord\UpdateRequest('test');
        $map = $update
            ->setPhotoUrl(null)
            ->getProperties();
        self::assertEquals(['PHOTO_URL'], $map['deleteAttribute']);
    }

    public function testDeletePhoneNumber()
    {
        $update = new UserRecord\UpdateRequest('test');
        $map = $update
            ->setPhoneNumber(null)
            ->getProperties();
        self::assertEquals(['phone'], $map['deleteProvider']);
    }

    public function testInvalidUpdatePhotoUrl()
    {
        $user = new UserRecord\UpdateRequest('test');

        try {
            $user->setPhotoUrl('');
            self::fail('No error thrown for empty photo url');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        try {
            $user->setPhotoUrl('not-a-url');
            self::fail('No error thrown for invalid photo url');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        self::assertTrue(true);
    }

    public function testInvalidUpdateEmail()
    {
        $user = new UserRecord\UpdateRequest('test');
        try {
            $user->setEmail(null);
            self::fail('No error thrown for null email');
        } catch (\TypeError $e) {
            // expected
        }

        try {
            $user->setEmail('');
            self::fail('No error thrown for empty email');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        try {
            $user->setEmail('not-an-email');
            self::fail('No error thrown for invalid email');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        self::assertTrue(true);
    }

    public function testInvalidUpdatePhoneNumber()
    {
        $user = new UserRecord\UpdateRequest('test');

        try {
            $user->setPhoneNumber('');
            self::fail('No error thrown for empty phone number');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        try {
            $user->setPhoneNumber('not-a-phone');
            self::fail('No error thrown for invalid phone number');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        self::assertTrue(true);
    }

    public function testInvalidUpdatePassword()
    {
        $user = new UserRecord\UpdateRequest('test');
        try {
            $user->setPassword(null);
            self::fail('No error thrown for null password');
        } catch (\TypeError $e) {
            // expected
        }

        try {
            $user->setPassword('aaaaa');
            self::fail('No error thrown for short password');
        } catch (InvalidArgumentException $e) {
            // expected
        }

        self::assertTrue(true);
    }

    public function testInvalidCustomClaims()
    {
        $update = new UserRecord\UpdateRequest('test');
        foreach (FirebaseUserManager::RESERVED_CLAIMS as $claim) {
            try {
                $update->setCustomClaims([$claim => 'value']);
                self::fail('No error thrown for reserved claim');
            } catch (InvalidArgumentException $e) {
                // expected
            }
        }
        self::assertTrue(true);
    }

    public function testLargeCustomClaims()
    {
        $value = str_repeat('a', 1001);
        $update = new UserRecord\UpdateRequest('test');
        $update->setCustomClaims(['key' => $value]);
        try {
            $update->getProperties();
            self::fail('No error thrown for large claims payload');
        } catch (InvalidArgumentException $e) {
            // expected
        }
        self::assertTrue(true);
    }

    public function testGeneratePasswordResetLinkNoEmail()
    {
        self::initializeAppForUserManagement();
        try {
            FirebaseAuth::getInstance()->generatePasswordResetLink(null);
            self::fail('No error thrown for null email');
        } catch (\TypeError $e) {
        }

        try {
            FirebaseAuth::getInstance()->generatePasswordResetLink('');
            self::fail('No error thrown for empty email');
        } catch (InvalidArgumentException $e) {
        }
        self::assertTrue(true);
    }

    public function testGeneratePasswordResetLinkWithSettings()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/generateEmailLink.json'))
        ]);
        $link = FirebaseAuth::getInstance()->generatePasswordResetLink('test@example.com', self::ACTION_CODE_SETTINGS());
        self::assertEquals('https://mock-oob-link.for.auth.tests', $link);
    }

    public function testGeneratePasswordResetLink()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/generateEmailLink.json'))
        ]);
        $link = FirebaseAuth::getInstance()->generatePasswordResetLink('test@example.com');
        self::assertEquals('https://mock-oob-link.for.auth.tests', $link);
    }

    public function testGenerateEmailVerificationLinkNoEmail()
    {
        self::initializeAppForUserManagement();
        try {
            FirebaseAuth::getInstance()->generateEmailVerificationLink(null);
            self::fail('No error thrown for null email');
        } catch (\TypeError $e) {
        }

        try {
            FirebaseAuth::getInstance()->generateEmailVerificationLink('');
            self::fail('No error thrown for empty email');
        } catch (InvalidArgumentException $e) {
        }
        self::assertTrue(true);
    }

    public function testGenerateEmailVerificationLinkWithSettings()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/generateEmailLink.json'))
        ]);
        $link = FirebaseAuth::getInstance()->generateEmailVerificationLink('test@example.com', self::ACTION_CODE_SETTINGS());
        self::assertEquals('https://mock-oob-link.for.auth.tests', $link);
    }

    public function testGenerateEmailVerificationLink()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/generateEmailLink.json'))
        ]);
        $link = FirebaseAuth::getInstance()->generateEmailVerificationLink('test@example.com');
        self::assertEquals('https://mock-oob-link.for.auth.tests', $link);
    }

    public function testGenerateSignInWithEmailLinkNoEmail()
    {
        self::initializeAppForUserManagement();
        try {
            FirebaseAuth::getInstance()->generateSignInWithEmailLink(null, self::ACTION_CODE_SETTINGS());
            self::fail('No error thrown for null email');
        } catch (\TypeError $e) {
        }

        try {
            FirebaseAuth::getInstance()->generateSignInWithEmailLink('', self::ACTION_CODE_SETTINGS());
            self::fail('No error thrown for empty email');
        } catch (InvalidArgumentException $e) {
        }
        self::assertTrue(true);
    }

    public function testGenerateSignInWithEmailLinkNullSettings()
    {
        self::initializeAppForUserManagement();
        try {
            FirebaseAuth::getInstance()
                ->generateSignInWithEmailLink('test@example.com', null);
            self::fail('No error thrown for null email');
        } catch (\TypeError $e) {
        }
        self::assertTrue(true);
    }

    public function testGenerateSignInWithEmailLinkWithSettings()
    {
        self::initializeAppForUserManagement([
            new Response(200, [], TestUtils::loadResource('/generateEmailLink.json'))
        ]);
        $link = FirebaseAuth::getInstance()->generateSignInWithEmailLink('test@example.com', self::ACTION_CODE_SETTINGS());
        self::assertEquals('https://mock-oob-link.for.auth.tests', $link);
    }

    public function testHttpErrorWithCode()
    {
        self::initializeAppForUserManagement([
            new Response(500, [], '{"error": {"message": "UNAUTHORIZED_DOMAIN"}}')
        ]);
        $auth = FirebaseAuth::getInstance();
        $userManager = $auth->getUserManager();
        try {
            $userManager->getEmailActionLink(EmailLinkType::PASSWORD_RESET(), 'test@example.com', null);
            self::fail('No exception thrown for HTTP error');
        } catch (FirebaseAuthException $e) {
            self::assertEquals('unauthorized-continue-uri', $e->getCode());
            self::assertTrue($e->getPrevious() instanceof BadResponseException);
        }
    }

    public function testUnexpectedHttpError()
    {
        self::initializeAppForUserManagement([
            new Response(500, [], '{}')
        ]);
        $auth = FirebaseAuth::getInstance();
        $userManager = $auth->getUserManager();
        try {
            $userManager->getEmailActionLink(EmailLinkType::PASSWORD_RESET(), 'test@example.com', null);
            self::fail('No exception thrown for HTTP error');
        } catch (FirebaseAuthException $e) {
            self::assertEquals('internal-error', $e->getCode());
            self::assertTrue($e->getPrevious() instanceof BadResponseException);
        }
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
