<?php

namespace Firebase\Tests\Auth;

use Carbon\Carbon;
use Firebase\Auth\FirebaseTokenUtils;
use Firebase\Auth\Internal\GooglePublicKeysManager;
use Firebase\Auth\Internal\IdTokenVerifier;
use Firebase\FirebaseApp;
use Firebase\FirebaseOptions;
use Firebase\Tests\Testing\MockServiceAccount;
use Firebase\Tests\Testing\TestOnlyImplFirebaseTrampolines;
use Google\Auth\CredentialsLoader;
use PHPUnit\Framework\TestCase;

class FirebaseTokenUtilsTest extends TestCase
{
    private const TEST_PROJECT_ID = 'test-project-id';

    final private static function CLOCK()
    {
        return Carbon::createFromTimestamp(2002000);
    }

    final private static function MOCK_CREDENTIALS()
    {
        return CredentialsLoader::makeInsecureCredentials();
    }

    final private static function MOCK_SERVICE_ACCOUNT()
    {
        return MockServiceAccount::EMPTY()->getServiceAccount();
    }

    protected function tearDown(): void
    {
        TestOnlyImplFirebaseTrampolines::clearInstancesForTest();
    }

    public function testCreateIdTokenVerifier()
    {
        $app = FirebaseApp::initializeApp(
            (new FirebaseOptions())
            ->setServiceAccount(self::MOCK_SERVICE_ACCOUNT())
            ->setProjectId(self::TEST_PROJECT_ID)
        );

        $idTokenVerifier = FirebaseTokenUtils::createIdTokenVerifier($app);
        self::assertEquals('verifyIdToken()', $idTokenVerifier->getMethod());
        self::assertEquals('ID token', $idTokenVerifier->getShortName());
        self::assertEquals('an ID token', $idTokenVerifier->getArticledShortName());
        self::assertEquals(
            'https://firebase.google.com/docs/auth/admin/verify-id-tokens',
            $idTokenVerifier->getDocUrl()
        );
        $this->verifyPublicKeysManager(
            $idTokenVerifier->getPublicKeysManager(),
            'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com'
        );
        $this->verifyJwtVerifier(
            $idTokenVerifier->getIdTokenVerifier(),
            'https://securetoken.google.com/test-project-id'
        );
    }

    public function testCreateIdTokenVerifierWithoutProjectId()
    {
        $app = FirebaseApp::initializeApp(
            (new FirebaseOptions())
                ->setServiceAccount(self::MOCK_SERVICE_ACCOUNT())
        );
        $this->expectExceptionMessage('Must initialize FirebaseApp with a project ID to call verifyIdToken()');
        FirebaseTokenUtils::createIdTokenVerifier($app);
    }

    public function testSessionCookieVerifier()
    {
        $app = FirebaseApp::initializeApp(
            (new FirebaseOptions())
                ->setServiceAccount(self::MOCK_SERVICE_ACCOUNT())
                ->setProjectId(self::TEST_PROJECT_ID)
        );

        $cookieVerifier = FirebaseTokenUtils::createSessionCookieVerifier($app);
        self::assertEquals('verifySessionCookie()', $cookieVerifier->getMethod());
        self::assertEquals('session cookie', $cookieVerifier->getShortName());
        self::assertEquals('a session cookie', $cookieVerifier->getArticledShortName());
        self::assertEquals(
            'https://firebase.google.com/docs/auth/admin/manage-cookies',
            $cookieVerifier->getDocUrl()
        );
        $this->verifyPublicKeysManager(
            $cookieVerifier->getPublicKeysManager(),
            'https://www.googleapis.com/identitytoolkit/v3/relyingparty/publicKeys'
        );
        $this->verifyJwtVerifier(
            $cookieVerifier->getIdTokenVerifier(),
            'https://session.firebase.google.com/test-project-id'
        );
    }

    public function testCreateSessionCookieVerifierWithoutProjectId()
    {
        $app = FirebaseApp::initializeApp(
            (new FirebaseOptions())
                ->setServiceAccount(self::MOCK_SERVICE_ACCOUNT())
        );
        $this->expectExceptionMessage('Must initialize FirebaseApp with a project ID to call verifySessionCookie()');
        FirebaseTokenUtils::createSessionCookieVerifier($app);
    }

    private function verifyPublicKeysManager(?GooglePublicKeysManager $publicKeysManager, string $certUrl)
    {
        self::assertNotNull($publicKeysManager);
        self::assertEquals($certUrl, $publicKeysManager->getPublicCertsEncodedUrl());
    }

    private function verifyJwtVerifier(?IdTokenVerifier $jwtVerifier, string $issuer)
    {
        self::assertNotNull($jwtVerifier);
        self::assertEquals($issuer, $jwtVerifier->getIssuer());
        self::assertEquals(self::TEST_PROJECT_ID, $jwtVerifier->getAudience());
    }
}
