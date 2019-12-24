<?php


namespace Firebase\Tests\Testing;


use Firebase\Util\Validator\Validator;
use Google\Auth\ApplicationDefaultCredentials;
use Google\Auth\CredentialsLoader;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;

class TestUtils
{
    const TEST_TIMEOUT_MILLIS = 7 * 1000;

    private const TEST_ADC_ACCESS_TOKEN = 'test-adc-access-token';

    private const TEST_URL = 'https://firebase.google.com';

    private static $defaultCredentials = null;

    public static function verifySignature(Token $token, array $keys = []) {
        foreach($keys as $key) {
            if($token->verify(new Sha256(), $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $vars
     */
    public static function setEnvironmentVariables(array $vars = []) {
        foreach($vars as $key => $var) {
            putenv(sprintf('%s=%s', $key, $var));
        }
    }

    /**
     * @param string[] $vars
     */
    public static function unsetEnvironmentVariables(array $vars = []) {
        foreach($vars as $var) {
            putenv($var);
        }
    }

    public static function getCertCredential(ServiceAccount $serviceAccount) {
        return CredentialsLoader::makeCredentials([], $serviceAccount->asArray());
    }

    /**
     * @param string $path
     * @return false|string
     */
    public static function loadResource(string $path) {
        $fileContents = file_get_contents(__DIR__ . '/../fixtures' . $path);
        Validator::checkArgument($fileContents !== false, sprintf('Failed to load resource: %s', $path));
        return $fileContents;
    }

    public static function getApplicationDefaultCredentials() {
        if(!is_null(self::$defaultCredentials)) {
            return self::$defaultCredentials;
        }
        $serviceAccountPath = realpath(__DIR__ . '/../fixtures/service_accounts/editor.json');
        self::setEnvironmentVariables([
            'GOOGLE_APPLICATION_CREDENTIALS' => $serviceAccountPath
        ]);
        return ApplicationDefaultCredentials::getCredentials();
    }
}