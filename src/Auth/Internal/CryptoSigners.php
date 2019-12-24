<?php


namespace Firebase\Auth\Internal;


use Firebase\Auth\Credential\CredentialHelpers;
use Firebase\FirebaseApp;
use Firebase\ImplFirebaseTrampolines;
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class CryptoSigners
{
    private const METADATA_SERVICE_URL = 'http://metadata.google.internal/computeMetadata/v1/instance/service-accounts/default/email';

    public static function getCryptoSigner(FirebaseApp $firebaseApp) {
        $credentials = ImplFirebaseTrampolines::getCredentials();

        if($credentials instanceof ServiceAccountCredentials) {
            return new ServiceAccountSigner($credentials);
        }

        $options = $firebaseApp->getOptions();

        // If the SDK was initialized with a service account email, use it with the IAM service
        // to sign bytes.
        $serviceAccountId = $options->getServiceAccountId();

        if(!empty($serviceAccountId)) {
            return new IAMSigner($serviceAccountId);
        }

        // Attempt to discover a service account email from the local Metadata service. Use it
        // with the IAM service to sign bytes.
        $request = new Request(
            'GET',
            self::METADATA_SERVICE_URL,
            ['Metadata-Flavor' => 'Google']
        );
        $response = (new Client())->send($request);
        $serviceAccountId = $response->getBody();
        return new IAMSigner($serviceAccountId);
    }
}
