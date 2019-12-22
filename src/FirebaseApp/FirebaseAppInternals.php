<?php

namespace Firebase\FirebaseApp;

use Carbon\Carbon;
use Firebase\Auth\Credential;
use Firebase\Util\Error\AppErrorCodes;
use Firebase\Util\Error\FirebaseAppError;
use Firebase\Util\Validator\Validator;

class FirebaseAppInternals {
    /**
     * @var bool
     */
    private $isDeleted = false;

    /**
     * @var FirebaseAccessToken
     */
    private $cacheToken;

    /**
     * @var Credential
     */
    private $credential;

    public function __construct(Credential $credential)
    {
        $this->credential = $credential;
    }

    public function getToken(bool $forceRefresh): FirebaseAccessToken {
        $expired = isset($this->cacheToken) && $this->cacheToken->getExpirationTime()->lessThan(Carbon::now());
        if($forceRefresh || $expired) {
            $result = $this->credential->getAccessToken();

            if(!Validator::isNonNullObject($result) || is_nan($result->getExpiresIn()) || !is_string($result->getAccessToken())) {
                throw new FirebaseAppError(
                    AppErrorCodes::INVALID_CREDENTIAL,
                    'Invalid access token generated. Valid access tokens must be an object with the "expires_in" (number) and "access_token" (string) properties.'
                );
            }

            $token = (new FirebaseAccessToken())
                ->setAccessToken($result->getAccessToken())
                ->setExpirationTime(Carbon::now()->addSeconds($result->getExpiresIn()));
        }
    }
}
