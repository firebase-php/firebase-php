<?php


namespace Firebase\Auth\Internal;

use Firebase\FirebaseApp;
use Firebase\ImplFirebaseTrampolines;
use Firebase\Auth\GoogleAuthLibrary\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class CryptoSigners
{
    private const METADATA_SERVICE_URL = 'http://metadata.google.internal/computeMetadata/v1/instance/service-accounts/default/email';

    public static function getCryptoSigner(FirebaseApp $firebaseApp) {
        $credentials = ImplFirebaseTrampolines::getCredentials($firebaseApp);

        if($credentials instanceof ServiceAccountCredentials) {
            return new ServiceAccountSigner($credentials);
        }

        $options = $firebaseApp->getOptions();

        // If the SDK was initialized with a service account email, use it with the IAM service
        // to sign bytes.
        $serviceAccountId = $options->getServiceAccountId();
        $httpClient = $options->getHttpClient() ?? new Client();

        if(!empty($serviceAccountId)) {
            return new IAMSigner($serviceAccountId, $httpClient);
        }

        // Attempt to discover a service account email from the local Metadata service. Use it
        // with the IAM service to sign bytes.
        $request = new Request(
            'GET',
            self::METADATA_SERVICE_URL,
            ['Metadata-Flavor' => 'Google']
        );
        $response = $httpClient->send($request);
        $serviceAccountId = $response->getBody();
        return new IAMSigner($serviceAccountId, $httpClient);
    }
}
