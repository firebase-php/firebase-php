<?php


namespace Firebase\Tests;

use Firebase\FirebaseOptions;
use Firebase\FirebaseOptionsBuilder;
use Firebase\Tests\Testing\MockServiceAccount;
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

    protected function setUp(): void
    {
        TestUtils::setApplicationDefaultCredentialsEnv();
    }

    protected function tearDown(): void
    {
        TestUtils::unsetEnvironmentVariables([CredentialsLoader::ENV_VAR]);
    }

    final private static function ALL_VALUES_OPTIONS()
    {
        return FirebaseOptions::builder()
            ->setCredentials(TestUtils::getCertCredential(MockServiceAccount::EDITOR()))
            ->setDatabaseUrl(self::FIREBASE_DB_URL)
            ->setProjectId(self::FIREBASE_PROJECT_ID)
            ->setStorageBucket(self::FIREBASE_STORAGE_BUCKET)
            ->build();
    }

    public function testCreateOptionsWithAllValuesSet()
    {
        $options = FirebaseOptions::builder()
            ->setCredentials(TestUtils::getCertCredential(MockServiceAccount::EDITOR()))
            ->setDatabaseUrl(self::FIREBASE_DB_URL)
            ->setStorageBucket(self::FIREBASE_STORAGE_BUCKET)
            ->setProjectId(self::FIREBASE_PROJECT_ID)
            ->build();
        $this->assertEquals(self::FIREBASE_DB_URL, $options->getDatabaseUrl());
        $this->assertEquals(self::FIREBASE_STORAGE_BUCKET, $options->getStorageBucket());
        $this->assertEquals(self::FIREBASE_PROJECT_ID, $options->getProjectId());
        $creds = $options->getCredentials();
        $this->assertNotNull($creds);
        $this->assertTrue($creds instanceof ServiceAccountCredentials);
        $this->assertEquals(TestUtils::getCertCredential(MockServiceAccount::EDITOR())->getClientName(), $creds->getClientName());
    }

    public function testCreateOptionsWithOnlyMandatoryValuesSet()
    {
        $options = FirebaseOptions::builder()
            ->setCredentials(TestUtils::getCertCredential(MockServiceAccount::EDITOR()))
            ->build();
        $this->assertNull($options->getDatabaseUrl());
        $this->assertNull($options->getStorageBucket());

        $creds = $options->getCredentials();
        $this->assertNotNull($creds);
        $this->assertTrue($creds instanceof ServiceAccountCredentials);
        $this->assertEquals(TestUtils::getCertCredential(MockServiceAccount::EDITOR())->getClientName(), $creds->getClientName());
    }

    public function testCreateOptionsWithCredentialMissing()
    {
        $this->expectException(InvalidArgumentException::class);
        FirebaseOptions::builder()->build();
    }

    public function testCreateOptionsWithNullCredential()
    {
        $this->expectException(InvalidArgumentException::class);
        FirebaseOptions::builder()->setCredentials(null)->build();
    }

    public function testCreateOptionsWithStorageBucketUrl()
    {
        $this->expectException(InvalidArgumentException::class);
        FirebaseOptions::builder()
            ->setCredentials(TestUtils::getCertCredential(MockServiceAccount::EDITOR()))
            ->setStorageBucket('gs://mock-storage-bucket')
            ->build();
    }

    public function testCheckToBuilderCreatesNewEquivalentInstance()
    {
        $options = (new FirebaseOptionsBuilder(self::ALL_VALUES_OPTIONS()))->build();
        $this->assertNotSame(self::ALL_VALUES_OPTIONS(), $options);
        $this->assertEquals(self::ALL_VALUES_OPTIONS()->getCredentials(), $options->getCredentials());
        $this->assertEquals(self::ALL_VALUES_OPTIONS()->getDatabaseUrl(), $options->getDatabaseUrl());
        $this->assertEquals(self::ALL_VALUES_OPTIONS()->getProjectId(), $options->getProjectId());
    }

    public function testNotEquals()
    {
        $options1 = FirebaseOptions::builder()
            ->setCredentials(TestUtils::getCertCredential(MockServiceAccount::EDITOR()))
            ->build();
        $options2 = FirebaseOptions::builder()
            ->setCredentials(TestUtils::getCertCredential(MockServiceAccount::EDITOR()))
            ->setDatabaseUrl('https://test.firebaseio.com')
            ->build();
        $this->assertNotEquals($options1, $options2);
    }
}
