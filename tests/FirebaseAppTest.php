<?php

namespace Firebase\Tests;

use Firebase\FirebaseApp;
use Firebase\FirebaseException;
use Firebase\FirebaseOptions;
use Firebase\ImplFirebaseTrampolines;
use Firebase\Tests\Testing\MockServiceAccount;
use Firebase\Tests\Testing\TestOnlyImplFirebaseTrampolines;
use Firebase\Tests\Testing\TestUtils;
use Firebase\Auth\GoogleAuthLibrary\ApplicationDefaultCredentials;
use Google\Auth\CredentialsLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class FirebaseAppTest extends TestCase
{
    final private static function OPTIONS()
    {
        return (new FirebaseOptions())
            ->setServiceAccount(TestUtils::getServiceAccount(MockServiceAccount::EDITOR()));
    }

    protected function setUp(): void
    {
        TestUtils::getServiceAccountFromDefaultCredentials();
    }

    protected function tearDown(): void
    {
        TestUtils::unsetEnvironmentVariables([FirebaseApp::FIREBASE_CONFIG_ENV_VAR, CredentialsLoader::ENV_VAR]);
        TestOnlyImplFirebaseTrampolines::clearInstancesForTest();
    }

    public function testNullAppName()
    {
        $this->expectException(InvalidArgumentException::class);
        FirebaseApp::initializeApp(self::OPTIONS(), null);
    }

    public function testEmptyAppName()
    {
        $this->expectException(InvalidArgumentException::class);
        FirebaseApp::initializeApp(self::OPTIONS(), '');
    }

    public function testGetInstancePersistedNotInitialized()
    {
        $name = 'myApp';
        FirebaseApp::initializeApp(self::OPTIONS(), $name);
        TestOnlyImplFirebaseTrampolines::clearInstancesForTest();

        $this->expectException(FirebaseException::class);
        FirebaseApp::getInstance($name);
    }

    public function testGetProjectIdFromOptions()
    {
        $dummyProjectId = 'explicit-project-id';
        $options = self::OPTIONS()
            ->setProjectId($dummyProjectId);
        $app = FirebaseApp::initializeApp($options, 'myApp');
        $projectId = ImplFirebaseTrampolines::getProjectId($app);
        $this->assertEquals($dummyProjectId, $projectId);
    }

    public function testGetProjectIdFromCredential()
    {
        $app = FirebaseApp::initializeApp(self::OPTIONS(), 'myApp');
        $projectId = ImplFirebaseTrampolines::getProjectId($app);
        $this->assertEquals("mock-project-id", $projectId);
    }

    public function testGetProjectIdFromEnvironment()
    {
        $variables = ['GCLOUD_PROJECT', 'GOOGLE_CLOUD_PROJECT'];
        foreach ($variables as $variable) {
            TestUtils::setEnvironmentVariables([$variable => 'project-id-1']);
            $options = (new FirebaseOptions())
                ->setServiceAccount(TestUtils::getServiceAccount(MockServiceAccount::EMPTY()));
            try {
                $app = FirebaseApp::initializeApp($options, "myApp_$variable");
                $projectId = ImplFirebaseTrampolines::getProjectId($app);
                $this->assertEquals('project-id-1', $projectId);
            } finally {
                TestUtils::unsetEnvironmentVariables([$variable]);
            }
        }
    }

    public function testProjectIdEnvironmentVariablePrecedence()
    {
        TestUtils::setEnvironmentVariables([
            'GCLOUD_PROJECT' => 'project-id-1',
            'GOOGLE_CLOUD_PROJECT' => 'project-id-2'
        ]);
        $options = (new FirebaseOptions())
            ->setServiceAccount(TestUtils::getServiceAccount(MockServiceAccount::EMPTY()));
        try {
            $app = FirebaseApp::initializeApp($options, 'myApp');
            $projectId = ImplFirebaseTrampolines::getProjectId($app);
            $this->assertEquals('project-id-2', $projectId);
        } finally {
            TestUtils::unsetEnvironmentVariables(['GCLOUD_PROJECT', 'GOOGLE_CLOUD_PROJECT']);
        }
    }

    public function testRehydratingDeletedInstanceThrows()
    {
        $name = 'myApp';
        $firebaseApp = FirebaseApp::initializeApp(self::OPTIONS(), $name);
        $firebaseApp->delete();
        TestOnlyImplFirebaseTrampolines::clearInstancesForTest();
        $this->expectException(FirebaseException::class);
        FirebaseApp::getInstance($name);
    }

    public function testDeleteDefaultApp()
    {
        $app = FirebaseApp::initializeApp(self::OPTIONS());
        $this->assertEquals($app, FirebaseApp::getInstance());
        $app->delete();
        $this->expectException(FirebaseException::class);
        FirebaseApp::getInstance();
    }

    public function testDeleteApp()
    {
        $name = 'myApp';
        $app = FirebaseApp::initializeApp(self::OPTIONS(), $name);
        $this->assertSame($app, FirebaseApp::getInstance($name));
        $app->delete();
        $this->expectException(FirebaseException::class);
        FirebaseApp::getInstance($name);

        // Verify we can reuse same app name
        $app2 = FirebaseApp::initializeApp(self::OPTIONS(), $name);
        $this->assertSame($app2, FirebaseApp::getInstance($name));
        $this->assertNotSame($app, $app2);
    }

    public function testGetApps()
    {
        $app1 = FirebaseApp::initializeApp(self::OPTIONS(), 'app1');
        $app2 = FirebaseApp::initializeApp(self::OPTIONS(), 'app2');
        $apps = FirebaseApp::getApps();
        $this->assertEquals(2, count($apps));
        $this->assertContains($app1, $apps);
        $this->assertContains($app2, $apps);
    }

    public function testGetNullApp()
    {
        FirebaseApp::initializeApp(self::OPTIONS(), 'app');
        $this->expectException(FirebaseException::class);
        FirebaseApp::getInstance(null);
    }

    public function testToString()
    {
        $app = FirebaseApp::initializeApp(self::OPTIONS(), 'app');
        $pattern = '/FirebaseApp\\{name=app}/';
        $this->assertRegExp($pattern, (string)$app);
    }

    public function testMissingInit()
    {
        $this->expectException(FirebaseException::class);
        FirebaseApp::getInstance();
    }

    private static function invokePublicInstanceMethodWithDefaultValues($instance = null, ?\ReflectionMethod $method = null)
    {
        $parameters = [];
        foreach ($method->getParameters() as $parameter) {
            if ($parameter->isDefaultValueAvailable()) {
                $parameters[] = $parameter->getDefaultValue();
            }
        }
        $method->invokeArgs($instance, $parameters);
    }

    public function testApiInitForNonDefaultApp()
    {
        $app = FirebaseApp::initializeApp(self::OPTIONS(), 'myApp');
        $this->assertFalse(ImplFirebaseTrampolines::isDefaultApp($app));
    }

    public function testApiInitForDefaultApp()
    {
        // Explicit initialization of FirebaseApp instance.
        $app = FirebaseApp::initializeApp(self::OPTIONS());
        $this->assertTrue(ImplFirebaseTrampolines::isDefaultApp($app));
    }

    public function testAppWithAuthVariableOverrides()
    {
        $authVariableOverrides = ['uid' => 'uid1'];
        $options = (new FirebaseOptions())
            ->setServiceAccount(TestUtils::getServiceAccount(MockServiceAccount::EDITOR()))
            ->setDatabaseAuthVariableOverride($authVariableOverrides);
        $app = FirebaseApp::initializeApp($options, 'testGetAppWithUid');
        $this->assertEquals('uid1', $app->getOptions()->getDatabaseAuthVariableOverride()['uid']);
        $token = TestOnlyImplFirebaseTrampolines::getToken($app);
        $this->assertTrue(!empty($token));
    }

    public function testEmptyFirebaseConfigString()
    {
        $this->setFirebaseConfigEnvironmentVariable('');
        $app = FirebaseApp::initializeApp();
        $this->assertNull($app->getOptions()->getProjectId());
        $this->assertNull($app->getOptions()->getStorageBucket());
        $this->assertNull($app->getOptions()->getDatabaseUrl());
        $this->assertEmpty($app->getOptions()->getDatabaseAuthVariableOverride());
    }

    public function testEmptyFirebaseConfigJSONObject()
    {
        $this->setFirebaseConfigEnvironmentVariable('{}');
        $app = FirebaseApp::initializeApp();
        $this->assertNull($app->getOptions()->getProjectId());
        $this->assertNull($app->getOptions()->getStorageBucket());
        $this->assertNull($app->getOptions()->getDatabaseUrl());
        $this->assertEmpty($app->getOptions()->getDatabaseAuthVariableOverride());
    }

    public function testInvalidFirebaseConfigFile()
    {
        $this->setFirebaseConfigEnvironmentVariable('firebase_config_invalid.json');
        $this->expectException(\InvalidArgumentException::class);
        FirebaseApp::initializeApp();
    }

    public function testInvalidFirebaseConfigString()
    {
        $this->setFirebaseConfigEnvironmentVariable('{...');
        $this->expectException(\InvalidArgumentException::class);
        FirebaseApp::initializeApp();
    }

    public function testFirebaseConfigMissingFile()
    {
        $this->setFirebaseConfigEnvironmentVariable('no_such_this_file_in_the_world.json');
        $this->expectException(\InvalidArgumentException::class);
        FirebaseApp::initializeApp();
    }

    public function testFirebaseConfigFileWithSomeKeysMissing()
    {
        $this->setFirebaseConfigEnvironmentVariable('firebase_config_partial.json');
        $app = FirebaseApp::initializeApp();
        $this->assertEquals('hipster-chat-mock', $app->getOptions()->getProjectId());
        $this->assertEquals('https://hipster-chat.firebaseio.mock', $app->getOptions()->getDatabaseUrl());
    }

    public function testValidFirebaseConfigFile()
    {
        $this->setFirebaseConfigEnvironmentVariable('firebase_config.json');
        $app = FirebaseApp::initializeApp();
        $this->assertEquals('hipster-chat-mock', $app->getOptions()->getProjectId());
        $this->assertEquals('hipster-chat.appspot.mock', $app->getOptions()->getStorageBucket());
        $this->assertEquals('https://hipster-chat.firebaseio.mock', $app->getOptions()->getDatabaseUrl());
        $this->assertEquals('testuser', $app->getOptions()->getDatabaseAuthVariableOverride()['uid']);
    }

    public function testEnvironmentVariableIgnored()
    {
        $this->setFirebaseConfigEnvironmentVariable('firebase_config.json');
        $app = FirebaseApp::initializeApp(self::OPTIONS());
        $this->assertNull($app->getOptions()->getProjectId());
        $this->assertNull($app->getOptions()->getStorageBucket());
        $this->assertNull($app->getOptions()->getDatabaseUrl());
        $this->assertEmpty($app->getOptions()->getDatabaseAuthVariableOverride());
    }

    public function testValidFirebaseConfigString()
    {
        $this->setFirebaseConfigEnvironmentVariable("{"
            . "\"databaseAuthVariableOverride\": {"
            .   "\"uid\":"
            .   "\"testuser\""
            . "},"
            . "\"databaseUrl\": \"https://hipster-chat.firebaseio.mock\","
            . "\"projectId\": \"hipster-chat-mock\","
            . "\"storageBucket\": \"hipster-chat.appspot.mock\""
            . "}");
        $app = FirebaseApp::initializeApp();
        $this->assertEquals('hipster-chat-mock', $app->getOptions()->getProjectId());
        $this->assertEquals('hipster-chat.appspot.mock', $app->getOptions()->getStorageBucket());
        $this->assertEquals('https://hipster-chat.firebaseio.mock', $app->getOptions()->getDatabaseUrl());
        $this->assertEquals('testuser', $app->getOptions()->getDatabaseAuthVariableOverride()['uid']);
    }

    public function testFirebaseConfigFileIgnoresInvalidKey()
    {
        $this->setFirebaseConfigEnvironmentVariable('firebase_config_invalid_key.json');
        $app = FirebaseApp::initializeApp();
        $this->assertEquals('hipster-chat-mock', $app->getOptions()->getProjectId());
    }

    public function testFirebaseConfigStringIgnoresInvalidKey()
    {
        $this->setFirebaseConfigEnvironmentVariable("{"
            . "\"databaseUareL\": \"https://hipster-chat.firebaseio.mock\","
            . "\"projectId\": \"hipster-chat-mock\""
            . "}");
        $app = FirebaseApp::initializeApp();
        $this->assertEquals('hipster-chat-mock', $app->getOptions()->getProjectId());
    }

    public function testFirebaseExceptionNullDetail()
    {
        $this->expectException(InvalidArgumentException::class);
        new FirebaseException(null);
    }

    public function testFirebaseExceptionEmptyDetail()
    {
        $this->expectException(InvalidArgumentException::class);
        new FirebaseException('');
    }

    private static function setFirebaseConfigEnvironmentVariable(string $configJson)
    {
        $configValue = null;
        if (empty($configJson) || $configJson[0] === '{') {
            $configValue = $configJson;
        } else {
            $configValueParts = pathinfo(__DIR__ . '/fixtures/' . $configJson);
            $configValue = sprintf('%s/%s', $configValueParts['dirname'], $configValueParts['basename']);
        }
        $envs = [FirebaseApp::FIREBASE_CONFIG_ENV_VAR => $configValue];
        TestUtils::setEnvironmentVariables($envs);
    }

    private static function getMockCredentialOptions()
    {
        return (new FirebaseOptionsBuilder())
            ->setCredentials(ApplicationDefaultCredentials::getCredentials([]))
            ->build();
    }
}
