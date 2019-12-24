<?php


namespace Firebase\Tests;


use Firebase\FirebaseOptions;
use Firebase\FirebaseOptionsBuilder;
use Firebase\Tests\Testing\ServiceAccount;
use Firebase\Tests\Testing\TestUtils;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\CredentialsLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class FirebaseOptionsTest extends TestCase
{
    private const FIREBASE_DB_URL = 'https://mock-project.firebaseio.com';
    private const FIREBASE_STORAGE_BUCKET = 'mock-storage-bucket';
    private const FIREBASE_PROJECT_ID = 'explicit-project-id';

    private static final function ALL_VALUES_OPTIONS() {
        return (new FirebaseOptionsBuilder())
            ->setDatabaseUrl(self::FIREBASE_DB_URL)
            ->setStorageBucket(self::FIREBASE_STORAGE_BUCKET)
            ->setProjectId(self::FIREBASE_PROJECT_ID)
            ->setCredentials(TestUtils::getCertCredential(ServiceAccount::EDITOR()))
            ->build();
    }

    public function testCreateOptionsWithAllValuesSet() {
        $options = (new FirebaseOptionsBuilder())
            ->setDatabaseUrl(self::FIREBASE_DB_URL)
            ->setStorageBucket(self::FIREBASE_STORAGE_BUCKET)
            ->setCredentials(TestUtils::getCertCredential(ServiceAccount::EDITOR()))
            ->setProjectId(self::FIREBASE_PROJECT_ID)
            ->setConnectTimeout(30000)
            ->setReadTimeout(60000)
            ->build();
        $this->assertEquals(self::FIREBASE_DB_URL, $options->getDatabaseUrl());
        $this->assertEquals(self::FIREBASE_STORAGE_BUCKET, $options->getStorageBucket());
        $this->assertEquals(self::FIREBASE_PROJECT_ID, $options->getProjectId());
        $this->assertEquals(30000, $options->getConnectTimeout());
        $this->assertEquals(60000, $options->getReadTimeout());

        $creds = $options->getCredentials();
        $this->assertNotNull($creds);
        $this->assertTrue($creds instanceof ServiceAccountCredentials);
        $this->assertEquals(TestUtils::getCertCredential(ServiceAccount::EDITOR())->getClientName(), $creds->getClientName());
    }

    public function testCreateOptionsWithOnlyMandatoryValuesSet() {
        $options = (new FirebaseOptionsBuilder())
            ->setCredentials(TestUtils::getCertCredential(ServiceAccount::EDITOR()))
            ->build();
        $this->assertNull($options->getDatabaseUrl());
        $this->assertNull($options->getStorageBucket());
        $this->assertEquals(0, $options->getConnectTimeout());
        $this->assertEquals(0, $options->getReadTimeout());

        $creds = $options->getCredentials();
        $this->assertNotNull($creds);
        $this->assertTrue($creds instanceof ServiceAccountCredentials);
        $this->assertEquals(TestUtils::getCertCredential(ServiceAccount::EDITOR())->getClientName(), $creds->getClientName());
    }

    public function testCreateOptionsWithCredentialMissing() {
        $this->expectException(InvalidArgumentException::class);
        (new FirebaseOptionsBuilder())->build()->getCredentials();
    }

    public function testCreateOptionsWithNullCredentials() {
        $this->expectException(InvalidArgumentException::class);
        (new FirebaseOptionsBuilder())
            ->setCredentials(null)
            ->build();
    }

    public function testCreateOptionsWithStorageBucketUrl() {
        $this->expectException(InvalidArgumentException::class);
        (new FirebaseOptionsBuilder())
            ->setCredentials(TestUtils::getCertCredential(ServiceAccount::EDITOR()))
            ->setStorageBucket('gs://mock-storage-bucket')
            ->build();
    }

    public function testCheckToBuilderCreatesNewEquivalentInstance() {
        $options = (new FirebaseOptionsBuilder(self::ALL_VALUES_OPTIONS()))->build();
        $this->assertNotSame(self::ALL_VALUES_OPTIONS(), $options);
        $this->assertEquals(self::ALL_VALUES_OPTIONS()->getCredentials(), $options->getCredentials());
        $this->assertEquals(self::ALL_VALUES_OPTIONS()->getDatabaseUrl(), $options->getDatabaseUrl());
        $this->assertEquals(self::ALL_VALUES_OPTIONS()->getProjectId(), $options->getProjectId());
        $this->assertEquals(self::ALL_VALUES_OPTIONS()->getConnectTimeout(), $options->getConnectTimeout());
        $this->assertEquals(self::ALL_VALUES_OPTIONS()->getReadTimeout(), $options->getReadTimeout());
    }

    public function testCreateOptionsWithInvalidConnectTimeout() {
        $this->expectException(InvalidArgumentException::class);
        (new FirebaseOptionsBuilder())
            ->setCredentials(TestUtils::getCertCredential(ServiceAccount::EDITOR()))
            ->setConnectTimeout(-1)
            ->build();
    }

    public function testCreateOptionsWithInvalidReadTimeout() {
        $this->expectException(InvalidArgumentException::class);
        (new FirebaseOptionsBuilder())
            ->setCredentials(TestUtils::getCertCredential(ServiceAccount::EDITOR()))
            ->setReadTimeout(-1)
            ->build();
    }

    public function testNotEquals() {
        $creds = TestUtils::getCertCredential(ServiceAccount::EDITOR());
        $options1 = (new FirebaseOptionsBuilder())
            ->setCredentials($creds)
            ->build();
        $options2 = (new FirebaseOptionsBuilder())
            ->setCredentials($creds)
            ->setDatabaseUrl('https://test.firebaseio.com')
            ->build();
        $this->assertNotEquals($options1, $options2);
    }
}
