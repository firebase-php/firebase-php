<?php

namespace Firebase\Tests\Auth;

use Firebase\Auth\FirebaseTokenVerifier;
use Firebase\Auth\FirebaseTokenVerifierImpl;
use Firebase\Auth\FirebaseTokenVerifierImplBuilder;
use Firebase\Auth\Internal\GooglePublicKeysManager;
use Firebase\Auth\Internal\GooglePublicKeysManagerBuilder;
use Firebase\Auth\Internal\IdTokenVerifier;
use Firebase\Auth\Internal\IdTokenVerifierBuilder;
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
}
