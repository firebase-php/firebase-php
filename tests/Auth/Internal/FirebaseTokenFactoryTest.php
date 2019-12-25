<?php

namespace Firebase\Tests\Auth\Internal;

use Firebase\Auth\FirebaseToken;
use Firebase\Auth\Internal\CryptoSigner;
use Firebase\Auth\Internal\FirebaseTokenFactory;
use Firebase\Tests\Testing\TestUtils;
use Google\Auth\ServiceAccountSignerTrait;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class FirebaseTokenFactoryTest extends TestCase
{
    private const USER_ID = 'fuber';

    private const EXTRA_CLAIMS = [
        'one' => 2,
        'three' => 'four'
    ];

    private const ISSUER = 'test-484@mg-test-1210.iam.gserviceaccount.com';

    public function testCheckSignatureForToken() {
        $res = openssl_pkey_new();
        $privateKey = openssl_get_privatekey($res);
        $tokenFactory = new FirebaseTokenFactory(self::TestCryptoSigner($privateKey));
        $jwt = $tokenFactory->createSignedCustomAuthTokenForUser(self::USER_ID, self::EXTRA_CLAIMS);
        $signedJwt = (new Parser())->parse($jwt);
        self::assertEquals((new Sha256())->getAlgorithmId(), $signedJwt->getHeader('alg', false));
        self::assertEquals(self::ISSUER, $signedJwt->getClaim('iss', false));
        self::assertEquals(self::ISSUER, $signedJwt->getClaim('sub', false));
        self::assertEquals(self::USER_ID, $signedJwt->getClaim('uid', false));

        $pubKey = openssl_pkey_get_details($res);
        self::assertTrue(TestUtils::verifySignature($signedJwt, [$pubKey['key']]));
    }

    public function testFailsWhenUidIsNull() {
        $res = openssl_pkey_new();
        $privateKey = openssl_get_privatekey($res);
        $tokenFactory = new FirebaseTokenFactory(self::TestCryptoSigner($privateKey));
        $this->expectException(InvalidArgumentException::class);
        $tokenFactory->createSignedCustomAuthTokenForUser(null);
    }

    public function testFailsWhenExtraClaimsContainsReservedKey() {
        $res = openssl_pkey_new();
        $privateKey = openssl_get_privatekey($res);
        $tokenFactory = new FirebaseTokenFactory(self::TestCryptoSigner($privateKey));
        $extraClaims = ['iss' => 'repeat issuer'];
        $this->expectException(InvalidArgumentException::class);
        $tokenFactory->createSignedCustomAuthTokenForUser(self::USER_ID, $extraClaims);
    }

    /**
     * @param resource $privateKey
     * @return CryptoSigner
     */
    private static function TestCryptoSigner($privateKey) {
        return new class(self::ISSUER, $privateKey) implements CryptoSigner {
            use ServiceAccountSignerTrait;
            /**
             * @var string
             */
            private $issuer;

            /**
             * @var resource
             */
            private $privateKey;

            public function __construct($issuer, $privateKey)
            {
                $this->issuer = $issuer;
                $this->privateKey = $privateKey;
            }

            public function sign(string $content)
            {
                openssl_sign($content, $binaryString, $this->privateKey, 'sha256WithRSAEncryption');
                return base64_encode($binaryString);
            }

            public function getAccount(): string
            {
                return $this->issuer;
            }
        };
    }
}
