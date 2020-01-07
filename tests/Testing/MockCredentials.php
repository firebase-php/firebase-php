<?php


namespace Firebase\Tests\Testing;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Google\Auth\CredentialsLoader;
use Google\Auth\SignBlobInterface;

class MockCredentials extends CredentialsLoader implements SignBlobInterface
{
    private $token;

    private $expiresAt;

    public function __construct(?string $token = null)
    {
        $this->token = $token;
    }

    /**
     * @param callable|null $httpHandler
     * @return array
     */
    public function fetchAuthToken(callable $httpHandler = null)
    {
        return [
            'access_token' => $this->token,
            'expires_at' => Carbon::now()->valueOf() + CarbonInterval::hours(1)->totalMilliseconds
        ];
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return null;
    }

    /**
     * @return array|null
     */
    public function getLastReceivedToken()
    {
        return null;
    }

    /**
     * @param string $stringToSign
     * @param bool $forceOpenssl
     * @return string
     */
    public function signBlob($stringToSign, $forceOpenssl = false)
    {
        return '';
    }

    /**
     * @param callable|null $httpHandler
     * @return string
     */
    public function getClientName(callable $httpHandler = null)
    {
        return '';
    }
}
