<?php

namespace Firebase\Tests\Auth;

use Carbon\CarbonInterval;
use Faker\Provider\Base;
use Firebase\Auth\ActionCodeSettings;
use Firebase\Auth\FirebaseAuth;
use Firebase\Auth\FirebaseAuthException;
use Firebase\Auth\FirebaseUserManager;
use Firebase\Auth\ImportUserRecord;
use Firebase\Auth\RevocationCheckDecorator;
use Firebase\Auth\SessionCookieOptions;
use Firebase\Auth\UserImportOptions;
use Firebase\Auth\UserInfo;
use Firebase\Auth\UserRecord\CreateRequest;
use Firebase\Auth\UserRecord\UpdateRequest;
use Firebase\Tests\Testing\IntegrationTestUtils;
use Firebase\Tests\Testing\RandomUser;
use FirebaseHash\Scrypt;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;

class FirebaseAuthIT extends TestCase
{
    private const VERIFY_CUSTOM_TOKEN_URL =
        'https://identitytoolkit.googleapis.com/v1/accounts:signInWithCustomToken';

    private const VERIFY_PASSWORD_URL =
        'https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword';

    private const RESET_PASSWORD_URL =
        'https://identitytoolkit.googleapis.com/v1/accounts:resetPassword';

    private const EMAIL_LINK_SIGN_IN_URL =
        'https://www.googleapis.com/identitytoolkit/v3/relyingparty/emailLinkSignin';

    private const ACTION_LINK_CONTINUE_URL = 'http://localhost/?a=1&b=2#c=3';

    private static $auth;

    protected function setUp(): void
    {
        $masterApp = IntegrationTestUtils::ensureDefaultApp();
        self::$auth = FirebaseAuth::getInstance($masterApp);
    }

    public function testGetNonExistingUser()
    {
        try {
            self::$auth->getUser('non.existing');
            self::fail('No error thrown for existing email');
        } catch (\Exception $e) {
            self::assertTrue($e instanceof FirebaseAuthException);
            self::assertEquals(FirebaseUserManager::USER_NOT_FOUND_ERROR, $e->getCode());
        }
    }

    public function testGetNonExistingUserByEmail()
    {
        try {
            self::$auth->getUserByEmail('non.existing@definitely.non.existing');
            self::fail('No error thrown for non existing email');
        } catch (FirebaseAuthException $e) {
            self::assertEquals(FirebaseUserManager::USER_NOT_FOUND_ERROR, $e->getCode());
        }
    }

    public function testUpdateNonExistingUser()
    {
        try {
            self::$auth->updateUser(
                new UpdateRequest('non.existing')
            );
            self::fail('No error thrown for non existing uid');
        } catch (FirebaseAuthException $e) {
            self::assertEquals(FirebaseUserManager::USER_NOT_FOUND_ERROR, $e->getCode());
        }
    }

    public function testDeleteNonExistingUser()
    {
        try {
            self::$auth->deleteUser('non.existing');
            self::fail('No error thrown for non existing uid');
        } catch (FirebaseAuthException $e) {
            self::assertEquals(FirebaseUserManager::USER_NOT_FOUND_ERROR, $e->getCode());
        }
    }

    public function testCreateUserWithParams()
    {
        $randomUser = RandomUser::create();
        $phone = $this->randomPhoneNumber();
        $user = (new CreateRequest())
            ->setUid($randomUser->getUid())
            ->setEmail($randomUser->getEmail())
            ->setPhoneNumber($phone)
            ->setDisplayName('Random User')
            ->setPhotoUrl('https://example.com/photo.png')
            ->setEmailVerified(true)
            ->setPassword('password');

        $userRecord = self::$auth->createUser($user);

        try {
            self::assertEquals($randomUser->getUid(), $userRecord->getUid());
            self::assertEquals('Random User', $userRecord->getDisplayName());
            self::assertEquals($randomUser->getEmail(), $userRecord->getEmail());
            self::assertEquals($phone, $userRecord->getPhoneNumber());
            self::assertEquals('https://example.com/photo.png', $userRecord->getPhotoUrl());
            self::assertTrue($userRecord->isEmailVerified());
            self::assertFalse($userRecord->isDisabled());
            self::assertEquals(2, count($userRecord->getProviderData()));
            $providers = [];
            /** @var UserInfo $provider */
            foreach ($userRecord->getProviderData() as $provider) {
                $providers[] = $provider->getProviderId();
            }
            self::assertTrue(in_array('password', $providers));
            self::assertTrue(in_array('phone', $providers));
            $this->checkRecreate($randomUser->getUid());
        } finally {
            self::$auth->deleteUser($userRecord->getUid());
        }
    }

    public function testUserLifecycle()
    {
        // Create user
        $userRecord = self::$auth->createUser(new CreateRequest());
        $uid = $userRecord->getUid();

        // Get user
        $userRecord = self::$auth->getUser($userRecord->getUid());
        self::assertEquals($uid, $userRecord->getUid());
        self::assertNull($userRecord->getDisplayName());
        self::assertNull($userRecord->getEmail());
        self::assertNull($userRecord->getPhoneNumber());
        self::assertNull($userRecord->getPhotoUrl());
        self::assertFalse($userRecord->isEmailVerified());
        self::assertFalse($userRecord->isDisabled());
        self::assertTrue($userRecord->getUserMetadata()->getCreationTimestamp() > 0);
        self::assertEquals(0, $userRecord->getUserMetadata()->getLastSignInTimestamp());
        self::assertEquals(0, count($userRecord->getProviderData()));
        self::assertTrue(empty($userRecord->getCustomClaims()));

        // update user
        $randomUser = RandomUser::create();
        $phone = $this->randomPhoneNumber();
        $request = $userRecord->updateRequest()
            ->setDisplayName('Updated Name')
            ->setEmail($randomUser->getEmail())
            ->setPhoneNumber($phone)
            ->setPhotoUrl('https://example.com/photo.png')
            ->setEmailVerified(true)
            ->setPassword('password');
        $userRecord = self::$auth->updateUser($request);
        self::assertEquals($uid, $userRecord->getUid());
        self::assertEquals('Updated Name', $userRecord->getDisplayName());
        self::assertEquals($randomUser->getEmail(), $userRecord->getEmail());
        self::assertEquals($phone, $userRecord->getPhoneNumber());
        self::assertTrue($userRecord->isEmailVerified());
        self::assertFalse($userRecord->isDisabled());
        self::assertEquals(2, count($userRecord->getProviderData()));
        self::assertTrue(empty($userRecord->getCustomClaims()));

        // get user by email
        $userRecord = self::$auth->getUserByEmail($userRecord->getEmail());
        self::assertEquals($uid, $userRecord->getUid());

        // disable user and remove properties
        $request = $userRecord->updateRequest()
            ->setPhotoUrl(null)
            ->setDisplayName(null)
            ->setPhoneNumber(null)
            ->setDisabled(true);
        $userRecord = self::$auth->updateUser($request);
        self::assertEquals($uid, $userRecord->getUid());
        self::assertNull($userRecord->getDisplayName());
        self::assertEquals($randomUser->getEmail(), $userRecord->getEmail());
        self::assertNull($userRecord->getPhoneNumber());
        self::assertNull($userRecord->getPhotoUrl());
        self::assertTrue($userRecord->isEmailVerified());
        self::assertTrue($userRecord->isDisabled());
        self::assertEquals(1, count($userRecord->getProviderData()));
        self::assertTrue(empty($userRecord->getCustomClaims()));

        // Delete user
        self::$auth->deleteUser($userRecord->getUid());

        try {
            self::$auth->getUser($userRecord->getUid());
            $this->fail('No error thrown for deleted user');
        } catch (\Exception $e) {
            self::assertTrue($e instanceof FirebaseAuthException);
            self::assertEquals(FirebaseUserManager::USER_NOT_FOUND_ERROR, $e->getCode());
        }
    }

    public function testListUsers()
    {
        $uids = [];
        $collected = 0;
        try {
            $uids[] = self::$auth->createUser((new CreateRequest())->setPassword('password'))->getUid();
            $uids[] = self::$auth->createUser((new CreateRequest())->setPassword('password'))->getUid();
            $uids[] = self::$auth->createUser((new CreateRequest())->setPassword('password'))->getUid();

            $page = self::$auth->listUsers(null);
            while (!is_null($page)) {
                foreach ($page->getValues() as $user) {
                    if (in_array($user->getUid(), $uids)) {
                        $collected++;
                        self::assertNotNull($user->getPasswordHash(), 'Missing passwordHash field. A common cause would be '
                            . '"forgetting to add the "Firebase Authentication Admin" permission. See "'
                            . 'instructions in CONTRIBUTING.md');
                        self::assertNotNull($user->getPasswordSalt());
                    }
                }
                $page = $page->getNextPage();
            }
            self::assertEquals(count($uids), $collected);
        } finally {
            foreach ($uids as $uid) {
                self::$auth->deleteUser($uid);
            }
        }
    }

    public function testCustomClaims()
    {
        $userRecord = self::$auth->createUser(new CreateRequest());
        $uid = $userRecord->getUid();

        try {
            self::assertTrue(empty($userRecord->getCustomClaims()));
            $expected = [
                'admin' => true,
                'package' => 'gold'
            ];
            self::$auth->setCustomUserClaims($uid, $expected);

            // Should have 2 claims
            $updatedUser = self::$auth->getUser($uid);
            self::assertEquals(2, count($updatedUser->getCustomClaims()));
            foreach ($expected as $key => $entry) {
                self::assertEquals($entry, $updatedUser->getCustomClaims()[$key]);
            }

            $customToken = self::$auth->createCustomToken($uid);
            $idToken = $this->signInWithCustomToken($customToken);
            $decoded = self::$auth->verifyIdToken($idToken);
            $result = $decoded->getClaims();

            foreach ($expected as $key => $entry) {
                self::assertEquals($entry, $result[$key]);
            }

            self::$auth->setCustomUserClaims($uid, null);
            $updatedUser = self::$auth->getUser($uid);
            self::assertTrue(empty($updatedUser->getCustomClaims()));
        } finally {
            self::$auth->deleteUser($uid);
        }
    }

    public function testCustomToken()
    {
        $customToken = self::$auth->createCustomToken('user1');
        $idToken = $this->signInWithCustomToken($customToken);
        $decodedToken = self::$auth->verifyIdToken($idToken);
        self::assertEquals('user1', $decodedToken->getUid());
    }

    public function testVerifyIdToken()
    {
        $customToken = self::$auth->createCustomToken('user2');
        $idToken = $this->signInWithCustomToken($customToken);
        $decodedToken = self::$auth->verifyIdToken($idToken, true);
        self::assertEquals('user2', $decodedToken->getUid());
        sleep(1);
        self::$auth->revokeRefreshToken('user2');
        $decodedToken = self::$auth->verifyIdToken($idToken, false);
        self::assertEquals('user2', $decodedToken->getUid());

        try {
            self::$auth->verifyIdToken($idToken, true);
            self::fail('expecting exception');
        } catch (FirebaseAuthException $e) {
            self::assertEquals(RevocationCheckDecorator::ID_TOKEN_REVOKED_ERROR, $e->getCode());
        }
        $idToken = $this->signInWithCustomToken($customToken);
        $decodedToken = self::$auth->verifyIdToken($idToken, true);
        self::assertEquals('user2', $decodedToken->getUid());
        self::$auth->deleteUser('user2');
    }

    public function testVerifySessionCookie()
    {
        $customToken = self::$auth->createCustomToken('user3');
        $idToken = $this->signInWithCustomToken($customToken);

        $options = SessionCookieOptions::builder()
            ->setExpiresIn(CarbonInterval::hour(1)->totalMilliseconds)
            ->build();
        $sessionCookie = self::$auth->createSessionCookie($idToken, $options);
        self::assertFalse(empty($sessionCookie));

        $decodedToken = self::$auth->verifySessionCookie($sessionCookie);
        self::assertEquals('user3', $decodedToken->getUid());
        $decodedToken = self::$auth->verifySessionCookie($sessionCookie, true);
        self::assertEquals('user3', $decodedToken->getUid());

        sleep(1);

        self::$auth->revokeRefreshToken('user3');
        $decodedToken = self::$auth->verifySessionCookie($sessionCookie, false);
        self::assertEquals('user3', $decodedToken->getUid());

        try {
            self::$auth->verifySessionCookie($sessionCookie, true);
            self::fail('expecting exception');
        } catch (FirebaseAuthException $e) {
            self::assertEquals(RevocationCheckDecorator::SESSION_COOKIE_REVOKED_ERROR, $e->getCode());
        }

        $idToken = $this->signInWithCustomToken($customToken);
        $sessionCookie = self::$auth->createSessionCookie($idToken, $options);
        $decodedToken = self::$auth->verifySessionCookie($sessionCookie, true);
        self::assertEquals('user3', $decodedToken->getUid());
        self::$auth->deleteUser('user3');
    }

    public function testCustomTokenWithClaims()
    {
        $devClaims = [
            'premium' => true,
            'subscription' => 'golden'
        ];

        $customToken = self::$auth->createCustomToken('user2', $devClaims);
        $idToken = $this->signInWithCustomToken($customToken);
        $decodedToken = self::$auth->verifyIdToken($idToken);
        self::assertEquals('user2', $decodedToken->getUid());
        self::assertTrue(isset($decodedToken->getClaims()['premium']));
        self::assertEquals('golden', $decodedToken->getClaims()['subscription']);
    }

    public function testImportUsers()
    {
        $randomUser = RandomUser::create();
        $user = ImportUserRecord::builder()
            ->setUid($randomUser->getUid())
            ->setEmail($randomUser->getEmail())
            ->build();

        $result = self::$auth->importUsers([$user]);
        self::assertEquals(1, $result->getSuccessCount());
        self::assertEquals(0, $result->getFailureCount());

        $savedUser = self::$auth->getUser($randomUser->getUid());
        self::assertEquals($randomUser->getEmail(), $savedUser->getEmail());
        self::$auth->deleteUser($randomUser->getUid());
    }

    public function testImportUsersWithPassword()
    {
        $passwordHash = getenv('TESTING_PASSWORD_HASH');
        $scryptKey = getenv('TESTING_SCRYPT_KEY');
        $randomUser = RandomUser::create();
        $user = ImportUserRecord::builder()
            ->setUid($randomUser->getUid())
            ->setEmail($randomUser->getEmail())
            ->setPasswordHash($passwordHash)
            ->setPasswordSalt(base64_encode('fish_sauce'))
            ->build();

        $saltSeparator = 'Bw==';

        $result = self::$auth->importUsers(
            [$user],
            UserImportOptions::withHash(
                Scrypt::builder()
                    ->setKey($scryptKey)
                    ->setSaltSeparator($saltSeparator)
                    ->setRounds(8)
                    ->setMemoryCost(14)
                    ->build()
            )
        );
        self::assertEquals(1, $result->getSuccessCount());
        self::assertEquals(0, $result->getFailureCount());

        try {
            $savedUser = self::$auth->getUser($randomUser->getUid());
            self::assertEquals($randomUser->getEmail(), $savedUser->getEmail());
            $idToken = $this->signInWithPassword($randomUser->getEmail(), 'password');
            self::assertFalse(empty($idToken));
        } finally {
            self::$auth->deleteUser($randomUser->getUid());
        }
    }

    public function testGeneratePasswordResetLink()
    {
        $user = RandomUser::create();
        self::$auth->createUser(
            (new CreateRequest())
                ->setUid($user->getUid())
                ->setEmail($user->getEmail())
                ->setEmailVerified(false)
                ->setPassword('password')
        );
        $link = self::$auth->generatePasswordResetLink(
            $user->getEmail(),
            ActionCodeSettings::builder()
                ->setUrl(self::ACTION_LINK_CONTINUE_URL)
                ->setHandleCodeInApp(false)
                ->build()
        );
        $linkParams = $this->parseLinkParameters($link);
        self::assertEquals(self::ACTION_LINK_CONTINUE_URL, $linkParams['continueUrl']);
        $email = $this->resetPassword(
            $user->getEmail(),
            'newPassword',
            $linkParams['oobCode']
        );
        self::assertEquals($user->getEmail(), $email);
        self::assertTrue(self::$auth->getUser($user->getUid())->isEmailVerified());
        self::$auth->deleteUser($user->getUid());
    }

    public function testGenerateEmailVerificationResetLink()
    {
        $user = RandomUser::create();
        self::$auth->createUser(
            (new CreateRequest())
                ->setUid($user->getUid())
                ->setEmail($user->getEmail())
                ->setEmailVerified(false)
                ->setPassword('password')
        );
        $link = self::$auth->generateEmailVerificationLink(
            $user->getEmail(),
            ActionCodeSettings::builder()
                ->setUrl(self::ACTION_LINK_CONTINUE_URL)
                ->setHandleCodeInApp(false)
                ->build()
        );
        $linkParams = $this->parseLinkParameters($link);
        self::assertEquals(self::ACTION_LINK_CONTINUE_URL, $linkParams['continueUrl']);
        self::assertEquals('verifyEmail', $linkParams['mode']);
        self::$auth->deleteUser($user->getUid());
    }

    public function testGenerateSignInWithEmailLink()
    {
        $user = RandomUser::create();
        self::$auth->createUser(
            (new CreateRequest())
                ->setUid($user->getUid())
                ->setEmail($user->getEmail())
                ->setEmailVerified(false)
                ->setPassword('password')
        );
        $link = self::$auth->generateSignInWithEmailLink(
            $user->getEmail(),
            ActionCodeSettings::builder()
                ->setUrl(self::ACTION_LINK_CONTINUE_URL)
                ->setHandleCodeInApp(false)
                ->build()
        );
        $linkParams = $this->parseLinkParameters($link);
        self::assertEquals(self::ACTION_LINK_CONTINUE_URL, $linkParams['continueUrl']);
        $idToken = $this->signInWithEmailLink($user->getEmail(), $linkParams['oobCode']);
        self::assertFalse(empty($idToken));
        self::assertTrue(self::$auth->getUser($user->getUid())->isEmailVerified());
        self::$auth->deleteUser($user->getUid());
    }

    private function signInWithPassword(string $email, string $password)
    {
        $url = self::VERIFY_PASSWORD_URL . '?key=' . IntegrationTestUtils::getApiKey();
        $content = [
            'email' => $email,
            'password' => $password,
            'returnSecureToken' => true
        ];
        $request = new Request(
            'POST',
            $url,
            [],
            json_encode($content)
        );
        $response = (new Client())->send($request);
        $json = json_decode($response->getBody(), true);
        return $json['idToken'];
    }

    private function signInWithEmailLink(string $email, string $oobCode)
    {
        $url = self::EMAIL_LINK_SIGN_IN_URL . '?key=' . IntegrationTestUtils::getApiKey();
        $content = [
            'email' => $email,
            'oobCode' => $oobCode
        ];
        $request = new Request(
            'POST',
            $url,
            [],
            json_encode($content)
        );
        $response = (new Client())->send($request);
        $json = json_decode($response->getBody(), true);
        return $json['idToken'];
    }

    private function parseLinkParameters(string $link)
    {
        $uri = new Uri($link);
        $result = [];
        $segments = explode('&', $uri->getQuery());

        foreach ($segments as $segment) {
            $pairs = explode('=', $segment);
            $result[$pairs[0]] = utf8_decode(urldecode($pairs[1]));
        }

        return $result;
    }

    private function resetPassword(
        string $email,
        string $newPassword,
        string $oobCode
    )
    {
        $url = self::RESET_PASSWORD_URL . '?key=' . IntegrationTestUtils::getApiKey();
        $content = [
            'email' => $email,
            'newPassword' => $newPassword,
            'oobCode' => $oobCode
        ];
        $request = new Request(
            'POST',
            $url,
            [],
            json_encode($content)
        );
        $response = (new Client())->send($request);
        $json = json_decode($response->getBody(), true);
        return $json['email'];
    }

    private function randomPhoneNumber()
    {
        $series = Base::regexify('[0-9]{10}');
        return '+1' . $series;
    }

    private function checkRecreate(?string $uid)
    {
        try {
            self::$auth->createUser(
                (new CreateRequest())->setUid($uid)
            );
            $this->fail("No error thrown for creating user with existing ID");
        } catch (\Exception $e) {
            self::assertTrue($e instanceof FirebaseAuthException);
            self::assertEquals('uid-already-exists', $e->getCode());
        }
    }

    private function signInWithCustomToken(string $customToken)
    {
        $url = sprintf('%s?key=%s', self::VERIFY_CUSTOM_TOKEN_URL, IntegrationTestUtils::getApiKey());
        $content = [
            'token' => $customToken,
            'returnSecureToken' => true
        ];
        $request = new Request(
            'POST',
            $url,
            [],
            json_encode($content)
        );
        $response = (new Client())->send($request);
        return json_decode($response->getBody(), true)['idToken'];
    }
}
