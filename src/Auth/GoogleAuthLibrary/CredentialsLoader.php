<?php


namespace Firebase\Auth\GoogleAuthLibrary;


use Firebase\Auth\GoogleAuthLibrary\Credentials\ServiceAccountCredentials;
use Google\Auth\Credentials\UserRefreshCredentials;

abstract class CredentialsLoader extends \Google\Auth\CredentialsLoader
{
    public static function makeCredentials($scope, array $jsonKey)
    {
        if (!array_key_exists('type', $jsonKey)) {
            throw new \InvalidArgumentException('json key is missing the type field');
        }
        if ($jsonKey['type'] == 'service_account') {
            return new ServiceAccountCredentials($scope, $jsonKey);
        }
        if ($jsonKey['type'] == 'authorized_user') {
            return new UserRefreshCredentials($scope, $jsonKey);
        }
        throw new \InvalidArgumentException('invalid value in the type field');
    }
}