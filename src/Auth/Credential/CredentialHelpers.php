<?php


namespace Firebase\Auth\Credential;


use Firebase\Auth\Credential;
use Firebase\Util\Error\AppErrorCodes;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAppError;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CredentialHelpers
{
    public static function credentialFromFile(string $filePath): Credential {
        $credentialsFile = self::readCredentialFromFile($filePath);
        if(!is_array($credentialsFile)) {
            throw new FirebaseAppError(
                AppErrorCodes::INVALID_CREDENTIAL,
                'Failed to parse contents of the credentials file as an object'
            );
        }
        if($credentialsFile['type'] === 'service_account') {
            return new CertCredential($credentialsFile);
        }
        if($credentialsFile['type'] === 'authorized_user') {
            return new RefreshTokenCredential($credentialsFile);
        }

        throw new FirebaseAppError(
            AppErrorCodes::INVALID_CREDENTIAL,
            'Invalid contents in the credentials file'
        );
    }

    public static function readCredentialFromFile(string $filePath): array {
        if(!is_string($filePath)) {
            throw new FirebaseAppError(
                AppErrorCodes::INVALID_CREDENTIAL,
                'Failed to parse credentials file: TypeError: path must be a string'
            );
        }
        $fileContent = file_get_contents($filePath);
        if($fileContent === false) {
            throw new FirebaseAppError(
                AppErrorCodes::INVALID_CREDENTIAL,
                "Failed to read credentials from file $filePath"
            );
        }

        return json_decode($fileContent, true);
    }

    /**
     * @param $credential
     * @return null
     */
    public static function tryGetCertificate($credential) {
        if($credential && self::isFirebaseCredential($credential)) {
            return $credential->getCertificate();
        }

        return null;
    }

    public static function isFirebaseCredential(Credential $credential) {
        return $credential instanceof FirebaseCredential;
    }

    public static function requestAccessToken(RequestInterface $request, array $options = []): GoogleOAuthAccessToken {
        try {

            $client = new Client();
            $response = $client->send($request, $options);
            $bodyStream = $response->getBody();
            $body = json_decode($bodyStream, true);
            if(!isset($body['access_token']) || !isset($body['expires_in'])) {
                throw new FirebaseAppError(
                    AppErrorCodes::INVALID_CREDENTIAL,
                    "Unexpected response while fetching access token: $bodyStream"
                );
            }
            $oauthToken = new GoogleOAuthAccessToken();
            $oauthToken
                ->setExpiresIn($body['expires_in'])
                ->setAccessToken($body['access_token']);

            return $oauthToken;
        } catch (RequestException $e) {
            $detail = $e instanceof ClientException ? self::getDetailFromResponse($e->getResponse()) : $e->getMessage();
            throw new FirebaseAppError(
                AppErrorCodes::INVALID_CREDENTIAL,
                'Error fetching access token: ' . $detail
            );
        }
    }

    public static function getDetailFromResponse(ResponseInterface $response) {
        $bodyStream = $response->getBody();
        $body = json_decode($bodyStream);

        if(is_array($body) && isset($body['error'])) {
            $detail = $body['error'];
            if(isset($body['error_description'])) {
                $detail .= '(' . $body['error_description'] . ')';
            }
            return $detail;
        }

        return 'Missing error payload';
    }
}
