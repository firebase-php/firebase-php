<?php


namespace Firebase\Auth\GoogleAuthLibrary;

use Google\Auth\Credentials\AppIdentityCredentials;
use Google\Auth\Credentials\GCECredentials;
use Google\Auth\FetchAuthTokenCache;
use Google\Auth\HttpHandler\HttpClientCache;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use GuzzleHttp\Client;
use Psr\Cache\CacheItemPoolInterface;

class ApplicationDefaultCredentials extends \Google\Auth\ApplicationDefaultCredentials
{
    /**
     * {@inheritDoc}
     */
    public static function getCredentials($scope = null, callable $httpHandler = null, array $cacheConfig = null, CacheItemPoolInterface $cache = null)
    {
        $creds = null;
        $jsonKey = CredentialsLoader::fromEnv()
            ?: CredentialsLoader::fromWellKnownFile();
        if (!$httpHandler) {
            if (!($client = HttpClientCache::getHttpClient())) {
                $client = new Client();
                HttpClientCache::setHttpClient($client);
            }
            $httpHandler = HttpHandlerFactory::build($client);
        }
        if (!is_null($jsonKey)) {
            $creds = CredentialsLoader::makeCredentials($scope, $jsonKey);
        } elseif (AppIdentityCredentials::onAppEngine() && !GCECredentials::onAppEngineFlexible()) {
            $creds = new AppIdentityCredentials($scope);
        } elseif (GCECredentials::onGce($httpHandler)) {
            $creds = new GCECredentials(null, $scope);
        }
        if (is_null($creds)) {
            throw new \DomainException(self::notFound());
        }
        if (!is_null($cache)) {
            $creds = new FetchAuthTokenCache($creds, $cacheConfig, $cache);
        }
        return $creds;
    }

    private static function notFound()
    {
        $msg = 'Could not load the default credentials. Browse to ';
        $msg .= 'https://developers.google.com';
        $msg .= '/accounts/docs/application-default-credentials';
        $msg .= ' for more information';
        return $msg;
    }
}
