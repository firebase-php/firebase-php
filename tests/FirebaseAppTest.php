<?php

namespace Firebase\Tests;

use Firebase\FirebaseApp;
use Firebase\FirebaseException;
use Firebase\FirebaseOptionsBuilder;
use Firebase\ImplFirebaseTrampolines;
use Firebase\Tests\Testing\ServiceAccount;
use Firebase\Tests\Testing\TestOnlyImplFirebaseTrampolines;
use Firebase\Tests\Testing\TestUtils;
use Google\Auth\ApplicationDefaultCredentials;
use Google\Auth\CredentialsLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class FirebaseAppTest extends TestCase
{
    final private static function OPTIONS() {
        return (new FirebaseOptionsBuilder())
            ->setCredentials(TestUtils::getCertCredential(ServiceAccount::EDITOR()))
            ->build();
    }

    protected function setUp(): void
    {
        TestUtils::getApplicationDefaultCredentials();
    }

    protected function tearDown(): void
    {
        TestUtils::unsetEnvironmentVariables([FirebaseApp::FIREBASE_CONFIG_ENV_VAR]);
        TestOnlyImplFirebaseTrampolines::clearInstancesForTest();
    }

    public function testNullAppName() {
        $this->expectException(InvalidArgumentException::class);
        FirebaseApp::initializeApp(self::OPTIONS(), null);
    }

    public function testEmptyAppName() {
        $this->expectException(InvalidArgumentException::class);
        FirebaseApp::initializeApp(self::OPTIONS(), '');
    }

    public function testGetInstancePersistedNotInitialized() {
        $name = 'myApp';
        FirebaseApp::initializeApp(self::OPTIONS(), $name);
        TestOnlyImplFirebaseTrampolines::clearInstancesForTest();

        $this->expectException(FirebaseException::class);
        FirebaseApp::getInstance($name);
    }

    public function testGetProjectIdFromOptions() {
        $dummyProjectId = 'explicit-project-id';
        $options = (new FirebaseOptionsBuilder(self::OPTIONS()))
            ->setProjectId($dummyProjectId)
            ->build();
        $app = FirebaseApp::initializeApp($options, 'myApp');
        $projectId = ImplFirebaseTrampolines::getProjectId($app);
        $this->assertEquals($dummyProjectId, $projectId);
    }

    public function testGetProjectIdFromCredential() {
        $app = FirebaseApp::initializeApp(self::OPTIONS(), 'myApp');
        $projectId = ImplFirebaseTrampolines::getProjectId($app);
        $this->assertEquals("mock-project-id", $projectId);
    }

    public function testGetProjectIdFromEnvironment() {
        $variables = ['GCLOUD_PROJECT', 'GOOGLE_CLOUD_PROJECT'];
        foreach ($variables as $variable) {
            TestUtils::setEnvironmentVariables([$variable => 'project-id-1']);
            $options = (new FirebaseOptionsBuilder())
                ->setCredentials(CredentialsLoader::makeInsecureCredentials())
                ->build();
            try {
                $app = FirebaseApp::initializeApp($options, "myApp_$variable");
                $projectId = ImplFirebaseTrampolines::getProjectId($app);
                $this->assertEquals('project-id-1', $projectId);
            } finally {
                TestUtils::unsetEnvironmentVariables([$variable]);
            }
        }
    }

    public function testProjectIdEnvironmentVariablePrecedence() {
        TestUtils::setEnvironmentVariables([
            'GCLOUD_PROJECT' => 'project-id-1',
            'GOOGLE_CLOUD_PROJECT' => 'project-id-2'
        ]);
        $options = (new FirebaseOptionsBuilder())
            ->setCredentials(CredentialsLoader::makeInsecureCredentials())
            ->build();
        try {
            $app = FirebaseApp::initializeApp($options, 'myApp');
            $projectId = ImplFirebaseTrampolines::getProjectId($app);
            $this->assertEquals('project-id-2', $projectId);
        } finally {
            TestUtils::unsetEnvironmentVariables(['GCLOUD_PROJECT', 'GOOGLE_CLOUD_PROJECT']);
        }
    }

    public function testRehydratingDeletedInstanceThrows() {
        $name = 'myApp';
        $firebaseApp = FirebaseApp::initializeApp(self::OPTIONS(), $name);
        $firebaseApp->delete();
        TestOnlyImplFirebaseTrampolines::clearInstancesForTest();
        $this->expectException(FirebaseException::class);
        FirebaseApp::getInstance($name);
    }

    public function testDeleteDefaultApp() {
        $app = FirebaseApp::initializeApp(self::OPTIONS());
        $this->assertEquals($app, FirebaseApp::getInstance());
        $app->delete();
        $this->expectException(FirebaseException::class);
        FirebaseApp::getInstance();
    }

    public function testDeleteApp() {
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

    public function testGetApps() {
        $app1 = FirebaseApp::initializeApp(self::OPTIONS(), 'app1');
        $app2 = FirebaseApp::initializeApp(self::OPTIONS(), 'app2');
        $apps = FirebaseApp::getApps();
        $this->assertEquals(2, count($apps));
        $this->assertContains($app1, $apps);
        $this->assertContains($app2, $apps);
    }

    public function testGetNullApp() {
        FirebaseApp::initializeApp(self::OPTIONS(), 'app');
        $this->expectException(FirebaseException::class);
        FirebaseApp::getInstance(null);
    }

    public function testToString() {
        $app = FirebaseApp::initializeApp(self::OPTIONS(), 'app');
        $pattern = '/FirebaseApp\\{name=app}/';
        $this->assertRegExp($pattern, (string)$app);
    }

    public function testInvokeAfterDeleteThrows() {
        $this->markTestIncomplete();
        $allowedToCallAfterDelete = [
            'delete',
            'getName',
            'isDefaultApp',
            '__toString'
        ];
        $app = FirebaseApp::initializeApp(self::OPTIONS(), 'myApp');
        $app->delete();
        $methods = get_class_methods($app);
        foreach($methods as $method) {
            $refMethod = new \ReflectionMethod(FirebaseApp::class, $method);
            if($refMethod->isPublic() && !$refMethod->isStatic()) {

            }
        }
    }

    public function testMissingInit() {
        $this->expectException(FirebaseException::class);
        FirebaseApp::getInstance();
    }

    private static function invokePublicInstanceMethodWithDefaultValues($instance = null, ?\ReflectionMethod $method = null) {
        $parameters = [];
        foreach ($method->getParameters() as $parameter) {
            if($parameter->isDefaultValueAvailable()) {
                $parameters[] = $parameter->getDefaultValue();
            }
        }
        $method->invokeArgs($instance, $parameters);
    }

    public function testApiInitForNonDefaultApp() {
        $app = FirebaseApp::initializeApp(self::OPTIONS(), 'myApp');
        $this->assertFalse(ImplFirebaseTrampolines::isDefaultApp($app));
    }

    public function testApiInitForDefaultApp() {
        // Explicit initialization of FirebaseApp instance.
        $app = FirebaseApp::initializeApp(self::OPTIONS());
        $this->assertTrue(ImplFirebaseTrampolines::isDefaultApp($app));
    }

    public function testTokenCaching() {
        $this->markTestIncomplete();
    }

    public function testTokenForceRefresh() {
        $this->markTestIncomplete();
    }

    public function testAppWithAuthVariableOverrides() {
        $authVariableOverrides = ['uid' => 'uid1'];
        $options = (new FirebaseOptionsBuilder(self::getMockCredentialOptions()))
            ->setDatabaseAuthVariableOverride($authVariableOverrides)
            ->build();
        $app = FirebaseApp::initializeApp($options, 'testGetAppWithUid');
        $this->assertEquals('uid1', $app->getOptions()->getDatabaseAuthVariableOverride()['uid']);
        $token = TestOnlyImplFirebaseTrampolines::getToken($app);
        $this->assertTrue(!empty($token));
    }

    public function testEmptyFirebaseConfigFile() {
        $this->expectException(FirebaseException::class);
        FirebaseApp::initializeApp();
    }

    private static function setFirebaseConfigEnvironmentVariable(string $configJson) {
        $configValue = null;
        if(empty($configJson) || $configJson[0] === '{') {
            $configValue = $configJson;
        } else {
            $configValue = file_get_contents(realpath(__DIR__ . '/fixtures/' . $configJson));
        }
        $envs = [FirebaseApp::FIREBASE_CONFIG_ENV_VAR => $configValue];
        TestUtils::setEnvironmentVariables($envs);
    }

    private static function getMockCredentialOptions() {
        return (new FirebaseOptionsBuilder())
            ->setCredentials(ApplicationDefaultCredentials::getCredentials(['scope/1']))
            ->build();
    }
}
