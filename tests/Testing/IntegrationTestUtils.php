<?php


namespace Firebase\Tests\Testing;


use Firebase\FirebaseApp;
use Firebase\FirebaseOptions;
use Firebase\FirebaseOptionsBuilder;

class IntegrationTestUtils
{
    private const IT_SERVICE_ACCOUNT_PATH = "integration_cert.json";

    private const IT_API_KEY_PATH = "integration_apikey.txt";

    /**
     * @var string
     */
    private static $apiKey;

    /**
     * @var FirebaseApp
     */
    private static $masterApp;

    /**
     * @var array
     */
    private static $serviceAccount;

    private static function itFilePath(string $path) {
        return realpath(__DIR__ . '/../fixtures/' . $path);
    }

    private static function ensureServiceAccount() {
        if(is_null(self::$serviceAccount)) {
            try {
                $contents = file_get_contents(self::itFilePath(self::IT_SERVICE_ACCOUNT_PATH));
                if($contents === false) {
                    throw new \Exception();
                }
                self::$serviceAccount = json_decode($contents, true);
            } catch (\Exception $e) {
                $msg = sprintf(
                    "Failed to read service account certificate from %s. "
                    . "Integration tests require a service account credential obtained from a Firebase "
                    . "project. See CONTRIBUTING.md for more details.", self::itFilePath(self::IT_SERVICE_ACCOUNT_PATH)
                );
                throw new \RuntimeException($msg);
            }
        }

        return self::$serviceAccount;
    }

    static function getServiceAccountCertificate() {
        return self::ensureServiceAccount();
    }

    static function getProjectId() {
        return self::$serviceAccount['project_id'] ?? '';
    }

    static function getDatabaseUrl() {
        return sprintf(
            'https://%s.firebaseio.com',
            self::getProjectId()
        );
    }

    static function getStorageBucket() {
        return sprintf(
            '%s.appspot.com',
            self::getProjectId()
        );
    }

    static function getApiKey() {
        if(is_null(self::$apiKey)) {
            try {
                self::$apiKey = file_get_contents(self::itFilePath(self::IT_API_KEY_PATH));
                if(self::$apiKey === false) {
                    throw new \Exception();
                }
            } catch (\Exception $e) {
                $msg = sprintf(
                    'Failed to read API key from %s. '
                    . 'Integration tests require an API key obtained from a Firebase '
                    . "project. See CONTRIBUTING.md for more details.", self::itFilePath(self::IT_API_KEY_PATH)
                );
                throw new \RuntimeException($msg);
            }
        }

        return self::$apiKey;
    }

    static function ensureDefaultApp() {
        if(is_null(self::$masterApp)) {
            $options = FirebaseOptions::builder()
                ->setDatabaseUrl(self::getDatabaseUrl())
                ->setStorageBucket(self::getStorageBucket())
                ->setCredentials(TestUtils::getCertCredential(self::getServiceAccountCertificate()))
                ->build();
            // TODO: test with Firestore
            self::$masterApp = FirebaseApp::initializeApp($options);
        }

        return self::$masterApp;
    }

    static function initApp(?string $name = null) {
        $options = (new FirebaseOptionsBuilder())
            ->setDatabaseUrl(self::getDatabaseUrl())
            ->setCredentials(TestUtils::getCertCredential(self::getServiceAccountCertificate()))
            ->build();
        return FirebaseApp::initializeApp($options, $name);
    }
}
