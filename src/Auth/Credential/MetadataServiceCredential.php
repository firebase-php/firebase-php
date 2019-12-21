<?php


namespace Firebase\Auth\Credential;


use Firebase\Auth\Credential;
use GuzzleHttp\Client;

class MetadataServiceCredential implements Credential
{
    private const GOOGLE_METADATA_SERVICE_HOST = 'metadata.google.internal';

    private const GOOGLE_METADATA_SERVICE_PATH = '/computeMetadata/v1/instance/service-accounts/default/token';

    public function getAccessToken(): GoogleOAuthAccessToken
    {
        $client = new Client();
        $response = $client
            ->post(
                'http://' . self::GOOGLE_METADATA_SERVICE_HOST . self::GOOGLE_METADATA_SERVICE_PATH,
                [
                    'headers' => [
                        'Metadata-Flavor' => 'Google'
                    ]
                ]
            );
        return CredentialHelpers::accessTokenBuilder($response);
    }

}
