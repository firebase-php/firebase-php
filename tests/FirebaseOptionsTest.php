<?php


namespace Firebase\Tests;

use Firebase\Auth\ServiceAccount;
use Firebase\FirebaseOptions;
use Firebase\Tests\Testing\MockServiceAccount;
use Firebase\Tests\Testing\TestUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class FirebaseOptionsTest extends TestCase
{
    private const FIREBASE_DB_URL = 'https://mock-project.firebaseio.com';
    private const FIREBASE_STORAGE_BUCKET = 'mock-storage-bucket';
    private const FIREBASE_PROJECT_ID = 'explicit-project-id';

    final private static function ALL_VALUES_OPTIONS()
    {
        return (new FirebaseOptions())
            ->setProjectId(self::FIREBASE_PROJECT_ID)
            ->setDatabaseUrl(self::FIREBASE_DB_URL)
            ->setStorageBucket(self::FIREBASE_STORAGE_BUCKET)
            ->setServiceAccount(MockServiceAccount::EDITOR()->getServiceAccount());
    }

    public function testCreateOptionsWithAllValuesSet()
    {
        $options = (new FirebaseOptions())
            ->setServiceAccount(TestUtils::getServiceAccount(MockServiceAccount::EDITOR()))
            ->setDatabaseUrl(self::FIREBASE_DB_URL)
            ->setStorageBucket(self::FIREBASE_STORAGE_BUCKET)
            ->setProjectId(self::FIREBASE_PROJECT_ID);

        $this->assertEquals(self::FIREBASE_DB_URL, $options->getDatabaseUrl());
        $this->assertEquals(self::FIREBASE_STORAGE_BUCKET, $options->getStorageBucket());
        $this->assertEquals(self::FIREBASE_PROJECT_ID, $options->getProjectId());

        $serviceAccount = $options->getServiceAccount();
        $this->assertNotNull($serviceAccount);
        $this->assertTrue($serviceAccount instanceof ServiceAccount);
        $this->assertEquals(TestUtils::getServiceAccount(MockServiceAccount::EDITOR())->getClientEmail(), $serviceAccount->getClientEmail());
    }

    public function testCreateOptionsWithOnlyMandatoryValuesSet()
    {
        $options = (new FirebaseOptions())
            ->setServiceAccount(TestUtils::getServiceAccount(MockServiceAccount::EDITOR()));
        $this->assertNull($options->getDatabaseUrl());
        $this->assertNull($options->getStorageBucket());

        $serviceAccount = $options->getServiceAccount();
        $this->assertNotNull($serviceAccount);
        $this->assertTrue($serviceAccount instanceof ServiceAccount);
        $this->assertEquals(TestUtils::getServiceAccount(MockServiceAccount::EDITOR())->getClientEmail(), $serviceAccount->getClientEmail());
    }

    public function testCreateOptionsWithServiceAccountMissing()
    {
        $this->expectException(InvalidArgumentException::class);
        (new FirebaseOptions())->getServiceAccount();
    }

    public function testCreateOptionsWithStorageBucketUrl()
    {
        $this->expectException(InvalidArgumentException::class);
        (new FirebaseOptions())
            ->setServiceAccount(TestUtils::getServiceAccount(MockServiceAccount::EDITOR()))
            ->setStorageBucket('gs://mock-storage-bucket')
            ->build();
    }

    public function testCheckToBuilderCreatesNewEquivalentInstance()
    {
        $options = self::ALL_VALUES_OPTIONS();
        $this->assertNotSame(self::ALL_VALUES_OPTIONS(), $options);
        $this->assertEquals(self::ALL_VALUES_OPTIONS()->getServiceAccount(), $options->getServiceAccount());
        $this->assertEquals(self::ALL_VALUES_OPTIONS()->getDatabaseUrl(), $options->getDatabaseUrl());
        $this->assertEquals(self::ALL_VALUES_OPTIONS()->getProjectId(), $options->getProjectId());
    }

    public function testNotEquals()
    {
        $sa = TestUtils::getServiceAccount(MockServiceAccount::EDITOR());
        $options1 = (new FirebaseOptions())
            ->setServiceAccount($sa);
        $options2 = (new FirebaseOptions())
            ->setServiceAccount($sa)
            ->setDatabaseUrl('https://test.firebaseio.com');
        $this->assertNotEquals($options1, $options2);
    }
}
