<?php


namespace Firebase\Auth\Internal;


use Google\Auth\Credentials;
use Google\Auth\OAuth2;

class ServiceAccountCredentials extends Credentials\ServiceAccountCredentials
{
    /**
     * @return OAuth2
     */
    public function getAuth(): OAuth2
    {
        return $this->auth;
    }

    /**
     * @param OAuth2 $auth
     * @return ServiceAccountCredentials
     */
    public function setAuth(OAuth2 $auth): ServiceAccountCredentials
    {
        $this->auth = $auth;
        return $this;
    }

}
