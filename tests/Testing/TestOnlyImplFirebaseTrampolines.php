<?php


namespace Firebase\Tests\Testing;

use Firebase\FirebaseApp;
use Firebase\FirebaseOptions;
use Google\Auth\CredentialsLoader;
use Firebase\Auth\ServiceAccount;

final class TestOnlyImplFirebaseTrampolines
{
    private function __construct()
    {
    }

    public static function clearInstancesForTest()
    {
        FirebaseApp::clearInstancesForTest();
    }

    public static function getToken(FirebaseApp $app = null, bool $forceRefresh = false)
    {
        /** @var ServiceAccount $serviceAccount */
        $serviceAccount = $app->getOptions()->getServiceAccount();
        $credentials = CredentialsLoader::makeCredentials(
            FirebaseOptions::FIREBASE_SCOPES,
            $serviceAccount->jsonSerialize()
        );
        $token = $credentials->fetchAuthToken(TestUtils::getMockHandler());
        return $token['access_token'];
    }

    public static function getCredentials(FirebaseOptions $options)
    {
        return $options->getCredentials();
    }
}
