<?php

namespace Firebase\Tests\Auth;

use Faker\Factory;
use Faker\Provider\Base;
use Firebase\Auth\FirebaseAuth;
use Firebase\Auth\FirebaseAuthException;
use Firebase\Auth\FirebaseUserManager;
use Firebase\Auth\Internal\DownloadAccountResponse;
use Firebase\Auth\UserInfo;
use Firebase\Auth\UserRecord\CreateRequest;
use Firebase\Auth\UserRecord\UpdateRequest;
use Firebase\Tests\Testing\IntegrationTestUtils;
use Firebase\Tests\Testing\RandomUser;
use Firebase\Tests\Testing\TestOnlyImplFirebaseTrampolines;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use phpseclib\Crypt\Random;
use PHPUnit\Framework\TestCase;

class FirebaseAuthIT extends TestCase
{
    private const VERIFY_CUSTOM_TOKEN_URL =
        'https://identitytoolkit.googleapis.com/v1/accounts:signInWithCustomToken';

    private const VERIFY_PASSWORD_URL =
        'https://www.googleapis.com/identitytoolkit/v3/relyingparty/verifyPassword';

    private const RESET_PASSWORD_URL =
        'https://www.googleapis.com/identitytoolkit/v3/relyingparty/resetPassword';

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

            $response = self::$auth->listUsers(null);
            $users = $response->getUsers();
            foreach ($users as $user) {
                if (in_array($user->getUid(), $uids)) {
                    $collected++;
                    self::assertNotNull($user->getPasswordHash(), 'Missing passwordHash field. A common cause would be '
                        . '"forgetting to add the "Firebase Authentication Admin" permission. See "'
                        . 'instructions in CONTRIBUTING.md');
                    self::assertNotNull($user->getPasswordSalt());
                }
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
