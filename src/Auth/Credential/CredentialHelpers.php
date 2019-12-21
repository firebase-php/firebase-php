<?php


namespace Firebase\Auth\Credential;


use Firebase\Util\Error\AppErrorCodes;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAppError;
use Psr\Http\Message\ResponseInterface;

class CredentialHelpers
{
    public static function accessTokenBuilder(ResponseInterface $response): GoogleOAuthAccessToken {
        $bodyStream = $response->getBody();
        $body = json_decode($bodyStream, true);
        if(!isset($body['access_token']) || !isset($body['expires_in'])) {
            throw new FirebaseAppError(
                new ErrorInfo(AppErrorCodes::INVALID_CREDENTIAL),
                "Unexpected response while fetching access token: $bodyStream"
            );
        }
        $oauthToken = new GoogleOAuthAccessToken();
        $oauthToken
            ->setExpiresIn($body['expires_in'])
            ->setAccessToken($body['access_token']);

        return $oauthToken;
    }
}
