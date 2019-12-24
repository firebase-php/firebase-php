<?php


namespace Firebase\Tests\Testing;


use Firebase\FirebaseApp;
use Firebase\FirebaseOptions;
use Google\Auth\ApplicationDefaultCredentials;
use Google\Auth\Credentials\ServiceAccountCredentials;

final class TestOnlyImplFirebaseTrampolines
{
    private function __construct() {}

    public static function clearInstancesForTest() {
        FirebaseApp::clearInstancesForTest();
    }

    public static function getToken(FirebaseApp $app = null, bool $forceRefresh = false) {
        /** @var ServiceAccountCredentials $credentials */
        $credentials = $app->getOptions()->getCredentials();
        $token = $credentials->getLastReceivedToken();
        return $token['access_token'];
    }

    public static function getCredentials(FirebaseOptions $options) {
        return $options->getCredentials();
    }
}
