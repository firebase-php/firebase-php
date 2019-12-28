<?php

namespace Firebase\Tests\Auth\Internal;

use Firebase\Auth\GoogleAuthLibrary\CredentialsLoader;
use Firebase\Auth\Internal\CryptoSigner;
use Firebase\Auth\Internal\CryptoSigners;
use Firebase\Auth\Internal\IAMSigner;
use Firebase\Auth\Internal\ServiceAccountSigner;
use Firebase\FirebaseApp;
use Firebase\FirebaseOptionsBuilder;
use Firebase\Tests\Testing\ServiceAccount;
use Firebase\Tests\Testing\TestOnlyImplFirebaseTrampolines;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class CryptoSignersTest extends TestCase
{
    protected function tearDown(): void
    {
        TestOnlyImplFirebaseTrampolines::clearInstancesForTest();
    }

    public function testServiceAccountCryptoSigner()
    {
        $creds = CredentialsLoader::makeCredentials([], ServiceAccount::EDITOR()->asArray());
        $expected = $creds->signBlob('foo');
        $signer = new ServiceAccountSigner($creds);
        $data = $signer->sign('foo');
        self::assertEquals($expected, $data);
    }

    public function testInvalidServiceAccountCryptoSigner()
    {
        $this->expectException(\TypeError::class);
        new ServiceAccountSigner(null);
    }

    public function testIAMCryptoSigner()
    {
        $signature = base64_encode('signed-bytes');
        $response = json_encode(['signature' => $signature]);

        $mock = new MockHandler([
            new Response(200, [], $response)
        ]);
        $httpClient = new Client([
            'handler' => HandlerStack::create($mock)
        ]);
        $signer = new IAMSigner('test-service-account@iam.gserviceaccount.com', $httpClient);

        $data = $signer->sign('foo');
        self::assertEquals('signed-bytes', $data);
        $url = 'https://iam.googleapis.com/v1/projects/-/serviceAccounts/test-service-account@iam.gserviceaccount.com:signBlob';
        self::assertEquals($url, $signer->getSignEndpoint());
    }

    public function testInvalidIAMCryptoSigner()
    {
        try {
            new IAMSigner(null);
            self::fail('No error thrown for null service account.');
        } catch (\Error $e) {
            self::assertTrue($e instanceof \TypeError);
        }
    }

    public function testMetadataService()
    {
        $signature = base64_encode('signed-bytes');
        $response = ['signature' => $signature];

        $mock = new MockHandler([
            new Response(200, [], 'metadata-server@iam.gserviceaccount.com'),
            new Response(200, [], json_encode($response))
        ]);
        $httpClient = new Client([
            'handler' => HandlerStack::create($mock)
        ]);
        $options = (new FirebaseOptionsBuilder())
            ->setCredentials(CredentialsLoader::makeInsecureCredentials())
            ->setHttpClient($httpClient)
            ->build();

        $app = FirebaseApp::initializeApp($options);
        $signer = CryptoSigners::getCryptoSigner($app);

        self::assertTrue($signer instanceof IAMSigner);
        $data = $signer->sign('foo');
        self::assertEquals('signed-bytes', $data);
        $url = 'https://iam.googleapis.com/v1/projects/-/serviceAccounts/metadata-server@iam.gserviceaccount.com:signBlob';
        self::assertEquals($url, $signer->getSignEndpoint());
    }

    public function testExplicitServiceAccountEmail()
    {
        $signature = base64_encode('signed-bytes');
        $response = ['signature' => $signature];

        $mock = new MockHandler([
            new Response(200, [], json_encode($response))
        ]);
        $httpClient = new Client([
            'handler' => HandlerStack::create($mock)
        ]);
        $options = (new FirebaseOptionsBuilder())
            ->setServiceAccountId('explicit-service-account@iam.gserviceaccount.com')
            ->setCredentials(CredentialsLoader::makeInsecureCredentials())
            ->setHttpClient($httpClient)
            ->build();

        $app = FirebaseApp::initializeApp($options);
        $signer = CryptoSigners::getCryptoSigner($app);

        self::assertTrue($signer instanceof IAMSigner);
        $data = $signer->sign('foo');
        self::assertEquals('signed-bytes', $data);
        $url = 'https://iam.googleapis.com/v1/projects/-/serviceAccounts/explicit-service-account@iam.gserviceaccount.com:signBlob';
        self::assertEquals($url, $signer->getSignEndpoint());
    }
}
