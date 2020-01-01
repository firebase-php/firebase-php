<?php

namespace Firebase\Tests\Auth;

use Firebase\Auth\FirebaseAuth;
use Firebase\Auth\FirebaseAuthException;
use Firebase\Auth\FirebaseToken;
use Firebase\Auth\FirebaseTokenVerifier;
use Firebase\Auth\FirebaseTokenVerifierImpl;
use Firebase\Auth\GoogleAuthLibrary\CredentialsLoader;
use Firebase\FirebaseApp;
use Firebase\FirebaseOptions;
use Firebase\Tests\Testing\MockServiceAccount;
use Firebase\Tests\Testing\ServiceAccount;
use Firebase\Tests\Testing\TestOnlyImplFirebaseTrampolines;
use Firebase\Tests\Testing\TestUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class FirebaseAuthTest extends TestCase
{
    private static function FIREBASE_OPTIONS()
    {
        return (new FirebaseOptions())
            ->setServiceAccount(TestUtils::getServiceAccount(MockServiceAccount::EDITOR()));
    }

    protected function tearDown(): void
    {
        TestOnlyImplFirebaseTrampolines::clearInstancesForTest();
    }

    public function testGetInstance()
    {
        FirebaseApp::initializeApp(self::FIREBASE_OPTIONS());
        $defaultAuth = FirebaseAuth::getInstance();
        self::assertNotNull($defaultAuth);
        self::assertSame($defaultAuth, FirebaseAuth::getInstance());
    }

    public function testGetInstanceForApp()
    {
        $app = FirebaseApp::initializeApp(self::FIREBASE_OPTIONS(), 'testGetInstanceForApp');
        $auth = FirebaseAuth::getInstance($app);
        self::assertNotNull($auth);
        self::assertSame($auth, FirebaseAuth::getInstance($app));
    }

    public function testAppDelete()
    {
        $app = FirebaseApp::initializeApp(self::FIREBASE_OPTIONS(), 'testAppDelete');
        $auth = FirebaseAuth::getInstance($app);
        self::assertNotNull($auth);
        $app->delete();
        $this->expectException(\Exception::class);
        FirebaseAuth::getInstance($app);
    }

    public function testInitAfterAppDelete()
    {
        $app = FirebaseApp::initializeApp(self::FIREBASE_OPTIONS(), 'testInitAfterAppDelete');
        $auth1 = FirebaseAuth::getInstance($app);
        self::assertNotNull($auth1);
        $app->delete();

        $app = FirebaseApp::initializeApp(self::FIREBASE_OPTIONS(), 'testInitAfterAppDelete');
        $auth2 = FirebaseAuth::getInstance($app);
        self::assertNotNull($auth2);
        self::assertNotSame($auth1, $auth2);
    }

    public function testProjectIdNotRequiredAtInitialization()
    {
        $options = FirebaseOptions::builder()
            ->setCredentials(TestUtils::getCertCredential(ServiceAccount::EDITOR()))
            ->build();
        $app = FirebaseApp::initializeApp($options, 'testProjectIdRequired');
        self::assertNotNull(FirebaseAuth::getInstance($app));
    }

    public function testAuthExceptionNullErrorCode()
    {
        $this->expectException(InvalidArgumentException::class);
        new FirebaseAuthException(null, 'test');
    }

    public function testAuthExceptionEmptyErrorCode()
    {
        $this->expectException(InvalidArgumentException::class);
        new FirebaseAuthException('', 'test');
    }

    public function testVerifyIdToken()
    {
        $tokenVerifier = self::MockTokenVerifier()::fromResult($this->getFirebaseToken('testUser'));
        $auth = $this->getAuthForIdTokenVerification($tokenVerifier);
        $token = $auth->verifyIdToken('idtoken');
        self::assertEquals('testUser', $token->getUid());
        self::assertEquals('idtoken', $tokenVerifier->getLastTokenString());
    }

    public function testVerifyIdTokenFailure()
    {
        $tokenVerifier = self::MockTokenVerifier()::fromException(
            new FirebaseAuthException('TEST_CODE', 'Test error message')
        );
        $auth = $this->getAuthForIdTokenVerification($tokenVerifier);

        try {
            $auth->verifyIdToken('idtoken');
        } catch (FirebaseAuthException $e) {
            self::assertEquals('TEST_CODE', $e->getCode());
            self::assertEquals('Test error message', $e->getMessage());
            self::assertEquals('idtoken', $tokenVerifier->getLastTokenString());
        }
    }

    public function testDefaultSessionCookieVerifier()
    {
        FirebaseApp::initializeApp(self::FIREBASE_OPTIONS());

        $tokenVerifier = FirebaseAuth::getInstance()
            ->getSessionCookieVerifier(false);
        self::assertTrue($tokenVerifier instanceof FirebaseTokenVerifierImpl);
        /**
         * @var FirebaseTokenVerifierImpl $tokenVerifier
         */
        $shortName = $tokenVerifier->getShortName();
        self::assertEquals('session cookie', $shortName);
    }

    public function testVerifySessionCookieWithNull()
    {
        $tokenVerifier = self::MockTokenVerifier()::fromResult(null);
        $tokenVerifier->setLastTokenString('_init_');
        $auth = $this->getAuthForSessionCookieVerification($tokenVerifier);


        try {
            $auth->verifySessionCookie(null);
        } catch (\Exception $e) {
            self::assertEquals('_init_', $tokenVerifier->getLastTokenString());
        }
    }

    public function testVerifySessionCookieWithEmptyString()
    {
        $tokenVerifier = self::MockTokenVerifier()::fromResult(null);
        $tokenVerifier->setLastTokenString('_init_');
        $auth = $this->getAuthForSessionCookieVerification($tokenVerifier);


        try {
            $auth->verifySessionCookie('');
        } catch (\Exception $e) {
            self::assertEquals('_init_', $tokenVerifier->getLastTokenString());
        }
    }

    public function testVerifySessionCookie()
    {
        $tokenVerifier = self::MockTokenVerifier()::fromResult(
            $this->getFirebaseToken('testUser')
        );
        $auth = $this->getAuthForSessionCookieVerification($tokenVerifier);
        $firebaseToken = $auth->verifySessionCookie('idtoken');
        self::assertEquals('testUser', $firebaseToken->getUid());
        self::assertEquals('idtoken', $tokenVerifier->getLastTokenString());
    }

    public function testVerifySessionCookieFailure()
    {
        $tokenVerifier = self::MockTokenVerifier()::fromException(
            new FirebaseAuthException('TEST_CODE', 'Test error message')
        );
        $auth = $this->getAuthForSessionCookieVerification($tokenVerifier);

        try {
            $auth->verifySessionCookie('idtoken');
        } catch (FirebaseAuthException $e) {
            self::assertEquals('TEST_CODE', $e->getCode());
            self::assertEquals('Test error message', $e->getMessage());
            self::assertEquals('idtoken', $tokenVerifier->getLastTokenString());
        }
    }

    public function testVerifySessionCookieWithCheckRevokedFailure()
    {
        $tokenVerifier = self::MockTokenVerifier()::fromException(
            new FirebaseAuthException('TEST_CODE', 'Test error message')
        );
        $auth = $this->getAuthForSessionCookieVerification($tokenVerifier);

        try {
            $auth->verifySessionCookie('idtoken', true);
        } catch (\Exception $e) {
            self::assertEquals('TEST_CODE', $e->getCode());
            self::assertEquals('Test error message', $e->getMessage());
            self::assertEquals('idtoken', $tokenVerifier->getLastTokenString());
        }
    }

    private function getFirebaseToken(?string $subject)
    {
        return new FirebaseToken(['sub' => $subject]);
    }

    private function getAuthForIdTokenVerification(?FirebaseTokenVerifier $tokenVerifier = null)
    {
        $app = FirebaseApp::initializeApp(self::FIREBASE_OPTIONS());


        return FirebaseAuth::builder()
            ->setFirebaseApp($app)
            ->setTokenFactory(null)
            ->setIdTokenVerifier($tokenVerifier)
            ->setCookieTokenVerifier(null)
            ->build();
    }

    private function getAuthForSessionCookieVerification(?FirebaseTokenVerifier $tokenVerifier = null)
    {
        $app = FirebaseApp::initializeApp(self::FIREBASE_OPTIONS());


        return FirebaseAuth::builder()
            ->setFirebaseApp($app)
            ->setTokenFactory(null)
            ->setIdTokenVerifier(null)
            ->setCookieTokenVerifier($tokenVerifier)
            ->build();
    }

    private static function MockTokenVerifier()
    {
        return new class implements FirebaseTokenVerifier {
            private $lastTokenString;

            private $result;

            private $exception;

            public function __construct(?FirebaseToken $result = null, ?FirebaseAuthException $exception = null)
            {
                $this->result = $result;
                $this->exception = $exception;
            }

            public function verifyToken(?string $token)
            {
                $this->lastTokenString = $token;

                if (!is_null($this->exception)) {
                    throw $this->exception;
                }

                return $this->result;
            }

            public static function fromResult(?FirebaseToken $result)
            {
                return new self($result, null);
            }

            public static function fromException(?FirebaseAuthException $exception)
            {
                return new self(null, $exception);
            }

            /**
             * @return mixed
             */
            public function getLastTokenString()
            {
                return $this->lastTokenString;
            }

            /**
             * @param mixed $lastTokenString
             * @return
             */
            public function setLastTokenString($lastTokenString)
            {
                $this->lastTokenString = $lastTokenString;
                return $this;
            }
        };
    }
}
