<?php


namespace Firebase\Auth\Credential;


use Firebase\Auth\Credential;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class MetadataServiceCredential implements Credential
{
    private const GOOGLE_METADATA_SERVICE_HOST = 'metadata.google.internal';

    private const GOOGLE_METADATA_SERVICE_PATH = '/computeMetadata/v1/instance/service-accounts/default/token';

    public function getAccessToken(): GoogleOAuthAccessToken
    {
        $request = new Request('POST', 'http://' . self::GOOGLE_METADATA_SERVICE_HOST . self::GOOGLE_METADATA_SERVICE_PATH, [
            'Metadata-Flavor' => 'Google'
        ]);
        return CredentialHelpers::requestAccessToken($request);
    }
}
