<?php

namespace Firebase\Tests\Auth;

use Firebase\Auth\FirebaseAuth;
use Firebase\Auth\FirebaseAuthException;
use Firebase\Auth\FirebaseToken;
use Firebase\Auth\FirebaseTokenVerifier;
use Firebase\FirebaseApp;
use Firebase\FirebaseOptions;
use Firebase\Tests\Testing\ServiceAccount;
use Firebase\Tests\Testing\TestOnlyImplFirebaseTrampolines;
use Firebase\Tests\Testing\TestUtils;
use PHPUnit\Framework\TestCase;

class FirebaseAuthTest extends TestCase
{
    private static function FIREBASE_OPTIONS() {
        return FirebaseOptions::builder()
            ->setCredentials(TestUtils::getCertCredential(ServiceAccount::EDITOR()))
            ->build();
    }

    protected function tearDown(): void
    {
        TestOnlyImplFirebaseTrampolines::clearInstancesForTest();
    }

    public function testGetInstance() {
        FirebaseApp::initializeApp(self::FIREBASE_OPTIONS());
        $defaultAuth = FirebaseAuth::getInstance();
        self::assertNotNull($defaultAuth);
        self::assertSame($defaultAuth, FirebaseAuth::getInstance());
    }

    public function testGetInstanceForApp() {}

    public function testAppDelete() {}

    public function testInvokeAfterAppDelete() {}

    public function testInitAfterAppDelete() {}

    public function testProjectIdNotRequiredAtInitialization() {}

    public function testAuthExceptionNullErrorCode() {}

    private function getFirebaseToken(?string $subject) {
        return new FirebaseToken(['sub' => $subject]);
    }


    private function getAuthForIdTokenVerification(?FirebaseTokenVerifier $tokenVerifier) {
        $app = FirebaseApp::initializeApp(self::FIREBASE_OPTIONS());


        return FirebaseAuth::builder()
            ->setFirebaseApp($app)
            ->setTokenFactory(null)
            ->setIdTokenVerifier(null)
            ->setCookieTokenVerifier(null)
            ->build();

    }

    private static function MockTokenVerifier() {
        return new class implements FirebaseTokenVerifier {
            private $lastTokenString;

            private $result;

            private $exception;

            private function __construct(?FirebaseToken $result, ?FirebaseAuthException $exception)
            {
                $this->result = $result;
                $this->exception = $exception;
            }

            public function verifyToken(?string $token)
            {
                $this->lastTokenString = $token;

                if(!is_null($this->exception)) {
                    throw $this->exception;
                }

                return $this->result;
            }

            static function fromResult(?FirebaseToken $result) {
                return new self($result, null);
            }

            static function fromException(?FirebaseAuthException $exception) {
                return new self(null, $exception);
            }
        };
    }
}
