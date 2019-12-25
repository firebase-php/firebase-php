<?php

namespace Firebase\Tests\Auth;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Firebase\Auth\FirebaseTokenVerifier;
use Firebase\Auth\FirebaseTokenVerifierImpl;
use Firebase\Auth\FirebaseTokenVerifierImplBuilder;
use Firebase\Auth\Internal\GooglePublicKeysManager;
use Firebase\Auth\Internal\GooglePublicKeysManagerBuilder;
use Firebase\Auth\Internal\IdTokenVerifier;
use Firebase\Auth\Internal\IdTokenVerifierBuilder;
use Firebase\Tests\Testing\IncorrectAlgorithmSigner;
use Firebase\Tests\Testing\ServiceAccount;
use Firebase\Tests\Testing\TestTokenFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class FirebaseTokenVerifierImplTest extends TestCase
{
    private const CUSTOM_TOKEN_AUDIENCE = 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit';

    private const LEGACY_CUSTOM_TOKEN =
        "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJkIjp7"
        . "InVpZCI6IjEiLCJhYmMiOiIwMTIzNDU2Nzg5fiFAIyQlXiYqKClfKy09YWJjZGVmZ2hpamtsbW5vcHF"
        . "yc3R1dnd4eXpBQkNERUZHSElKS0xNTk9QUVJTVFVWV1hZWiwuLzsnW11cXDw"
        . "-P1wie318In0sInYiOjAsImlhdCI6MTQ4MDk4Mj"
        . "U2NH0.ZWEpoHgIPCAz8Q-cNFBS8jiqClTJ3j27yuRkQo-QxyI";

    private const TEST_TOKEN_ISSUER = 'https://test.token.issuer';

    /**
     * @var FirebaseTokenVerifier
     */
    private $tokenVerifier;

    /**
     * @var TestTokenFactory
     */
    private $tokenFactory;

    protected function setUp(): void
    {
        $serviceAccount = ServiceAccount::EDITOR();
        $pubKeysManager = $this->newPublicKeysManager($serviceAccount->getCert());
        $this->tokenVerifier = $this->newTestTokenVerifier($pubKeysManager);
        $this->tokenFactory = new TestTokenFactory($serviceAccount->getPrivateKey(), self::TEST_TOKEN_ISSUER);
    }

    public function testVerifyToken() {
        $token = $this->tokenFactory->createToken();
        $firebaseToken = $this->tokenVerifier->verifyToken($token);
        $this->assertEquals(self::TEST_TOKEN_ISSUER, $firebaseToken->getIssuer());
        $this->assertEquals(TestTokenFactory::UID, $firebaseToken->getUid());
    }

    public function testVerifyTokenWithoutKeyId() {
        $token = $this->createTokenWithoutKeyId();
        $this->expectExceptionMessageMatches('/^(Firebase test token has no "kid" claim\.)*/');
        $this->tokenVerifier->verifyToken($token);
    }

    public function testVerifyTokenFirebaseCustomToken() {
        $token = $this->createCustomToken();
        $this->expectExceptionMessageMatches('/^(verifyTestToken() expects a test token, but was given a custom token\.)*/');
        $this->tokenVerifier->verifyToken($token);
    }

    public function testVerifyTokenIncorrectAudience() {
        $token = $this->createTokenWithIncorrectAudience();
        $this->expectExceptionMessageMatches('/^(Firebase test token has incorrect "aud" (audience) claim\.)*/');
        $this->tokenVerifier->verifyToken($token);
    }

    public function testVerifyTokenIncorrectIssuer() {
        $token = $this->createTokenWithIncorrectIssuer();
        $this->expectExceptionMessageMatches('/^(Firebase test token has incorrect "iss" (issuer) claim\.)*/');
        $this->tokenVerifier->verifyToken($token);
    }

    public function testVerifyTokenMissingSubject() {
        $token = $this->createTokenWithSubject(null);
        $this->expectExceptionMessageMatches('/^(Firebase test token has no "sub" (subject) claim.)*/');
        $this->tokenVerifier->verifyToken($token);
    }

    public function testVerifyTokenEmptySubject() {
        $token = $this->createTokenWithSubject('');
        $this->expectExceptionMessageMatches('/^(Firebase test token has an empty string "sub" (subject) claim.)*/');
        $this->tokenVerifier->verifyToken($token);
    }

    public function testVerifyTokenLongSubject() {
        $token = $this->createTokenWithSubject(str_repeat('a', 129));
        $this->expectExceptionMessageMatches('/^(Firebase test token has "sub" (subject) claim longer than 128 characters.)*/');
        $this->tokenVerifier->verifyToken($token);
    }

    public function testVerifyTokenIssuedAtInFuture() {
        $tenMinutesIntoTheFuture = Carbon::now()->timestamp + CarbonInterval::minutes(10)->totalSeconds;
        $token = $this->createTokenWithTimestamps(
            $tenMinutesIntoTheFuture,
            $tenMinutesIntoTheFuture + CarbonInterval::hour(1)->totalSeconds
        );
        $this->expectExceptionMessageMatches('/^(Firebase test token has expired or is not yet valid.)*/');
        $this->tokenVerifier->verifyToken($token);
    }

    public function testVerifyTokenExpired() {
        $twoHoursInPast = Carbon::now()->timestamp - CarbonInterval::hours(2)->totalSeconds;
        $token = $this->createTokenWithTimestamps(
            $twoHoursInPast,
            $twoHoursInPast + CarbonInterval::hour(1)->totalSeconds
        );
        $this->expectExceptionMessageMatches('/^(Firebase test token has expired or is not yet valid.)*/');
        $this->tokenVerifier->verifyToken($token);
    }

    private function newPublicKeysManager(?string $cert) {
        $serviceAccountCert = json_encode([
            TestTokenFactory::PRIVATE_KEY_ID => $cert
        ]);
        $mock = new MockHandler([
            new Response(200, [], $serviceAccountCert)
        ]);
        return (
            new GooglePublicKeysManagerBuilder(
                new Client(['handler' => HandlerStack::create($mock)])
            ))
            ->setPublicCertsEncodeUrl('https://test.cert.url')
            ->build();
    }

    private function newTestTokenVerifier(?GooglePublicKeysManager $publicKeysManager) {
        return FirebaseTokenVerifierImpl::builder()
            ->setShortName('test token')
            ->setMethod('verifyTestToken()')
            ->setDocUrl('https://test.doc.url')
            ->setPublicKeysManager($publicKeysManager)
            ->setIdTokenVerifier($this->newIdTokenVerifier())
            ->build();
    }

    private function newIdTokenVerifier() {
        $tokenVerifier = new IdTokenVerifier();
        $tokenVerifier->setAudience(TestTokenFactory::PROJECT_ID);
        $tokenVerifier->setIssuer(self::TEST_TOKEN_ISSUER);

        return $tokenVerifier;
    }

    private function createTokenWithoutKeyId() {
        $headers = $this->tokenFactory->createHeader();
        unset($headers['kid']);
        return $this->tokenFactory->createToken($headers);
    }

    private function createTokenWithSubject(?string $sub) {
        $payload = $this->tokenFactory->createTokenPayload();
        if(is_null($sub)) {
            unset($payload['sub']);
        } else {
            $payload['sub'] = $sub;
        }
        return $this->tokenFactory->createToken(null, $payload);
    }

    private function createCustomToken() {
        $headers = $this->tokenFactory->createHeader();
        unset($headers['kid']);
        $payload = $this->tokenFactory->createTokenPayload();
        $payload['aud'] = self::CUSTOM_TOKEN_AUDIENCE;
        return $this->tokenFactory->createToken($headers, $payload);
    }

    private function createTokenWithIncorrectAlgorithm() {
        return $this->tokenFactory->createToken(null, null, new IncorrectAlgorithmSigner());
    }

    private function createTokenWithIncorrectAudience() {
        $payload = $this->tokenFactory->createTokenPayload();
        $payload['aud'] = 'invalid-audience';
        return $this->tokenFactory->createToken(null, $payload);
    }

    private function createTokenWithIncorrectIssuer() {
        $payload = $this->tokenFactory->createTokenPayload();
        $payload['iss'] = 'https://incorrect.issuer.prefix/' . TestTokenFactory::PROJECT_ID;
        return $this->tokenFactory->createToken(null, $payload);
    }

    private function createTokenWithTimestamps(int $issueAtSeconds, int $expirationSeconds) {
        $payload = $this->tokenFactory->createTokenPayload();
        $payload['iat'] = $issueAtSeconds;
        $payload['exp'] = $expirationSeconds;
        return $this->tokenFactory->createToken(null, $payload);
    }
}
