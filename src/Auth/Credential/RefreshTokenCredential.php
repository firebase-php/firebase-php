<?php


namespace Firebase\Auth\Credential;


use Firebase\Auth\Credential;
use Firebase\Util\Error\AuthClientErrorCode;
use Firebase\Util\Error\FirebaseAppError;
use GuzzleHttp\Client;

class RefreshTokenCredential implements Credential
{
    private const REFRESH_TOKEN_HOST = 'www.googleapis.com';

    private const REFRESH_TOKEN_PATH = '/oauth2/v4/token';

    /**
     * @var RefreshToken
     */
    private $refreshToken;

    public function __construct($refreshTokenPathOrObject)
    {
        if(is_string($refreshTokenPathOrObject)) {
            $refreshToken = RefreshToken::fromPath($refreshTokenPathOrObject);
            if(!$refreshToken) {
                throw new FirebaseAppError(
                    AuthClientErrorCode::NOT_FOUND['code'],
                    'The file referred to by the $refreshTokenPathOrObject parameter(' . $refreshTokenPathOrObject . ') was not found.'
                );
            }
            $this->refreshToken = $refreshToken;
        } else {
            $this->refreshToken = new RefreshToken($refreshTokenPathOrObject);
        }
    }

    public function getAccessToken(): GoogleOAuthAccessToken
    {
        $client = new Client();
        $response = $client->post(
            'https://' . self::REFRESH_TOKEN_HOST . self::REFRESH_TOKEN_PATH,
            [
                'form_params' => [
                    'client_id' => $this->refreshToken->getClientId(),
                    'client_secret' => $this->refreshToken->getClientSecret(),
                    'refresh_token' => $this->refreshToken->getRefreshToken(),
                    'grant_type' => 'refresh_token'
                ]
            ]
        );
        return CredentialHelpers::accessTokenBuilder($response);
    }
}
