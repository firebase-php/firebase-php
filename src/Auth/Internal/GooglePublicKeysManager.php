<?php


namespace Firebase\Auth\Internal;


use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class GooglePublicKeysManager
{
    private const REFRESH_SKEW_MILLIS = 300000;

    private const MAX_AGE_PATTERN = '/\s*max-age\s*=\s*(?P<cacheControl>\d+)\s*/';

    /**
     * @var string[]
     */
    private $publicKeys;

    /**
     * @var string string
     */
    private $publicCertsEncodedUrl;

    /**
     * @var int
     */
    private $expirationTimeMilliseconds;

    public function __construct(GooglePublicKeysManagerBuilder $builder = null)
    {
        if(!$builder) {
            return;
        }
        $this->publicCertsEncodedUrl = $builder->getPublicCertsEncodeUrl();
    }

    /**
     * @return string[]
     */
    public function getPublicKeys()
    {
        $publicKeys = [];
        try {
            if(empty($this->publicKeys) || Carbon::now()->valueOf() + self::REFRESH_SKEW_MILLIS > $this->expirationTimeMilliseconds) {
                $this->refresh();
            }
            $publicKeys = $this->publicKeys;
        } catch (\Exception $e) {}

        return $publicKeys;
    }

    /**
     * @return GooglePublicKeysManager|null
     */
    public function refresh() {
        /** @var GooglePublicKeysManager $instance */
        $instance = new GooglePublicKeysManager();

        try {
            $this->publicKeys = [];
            $certRequest = new Request('GET', $this->publicCertsEncodedUrl);
            $certResponse = (new Client())->send($certRequest);
            $this->expirationTimeMilliseconds = Carbon::now()->valueOf() + $this->getCacheTimeInSec($certResponse) * 1000;
            $certs = json_decode($certResponse->getBody());

            foreach($certs as $kid => $cert) {
                $this->publicKeys[$kid] = $cert;
            }

            $instance = $this;
        } catch (\Exception $e) {}

        return $instance;
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function getCacheTimeInSec(ResponseInterface $response) {
        $cacheTimeInSec = 0;
        if($response->hasHeader('cache-control')) {
            $cacheControl = $response->getHeader('cache-control')[0];
            $parts = explode(',', $cacheControl);
            foreach ($parts as $part) {
                preg_match(self::MAX_AGE_PATTERN, $part, $matches);
                if(isset($matches['cacheControl'])) {
                    $cacheTimeInSec = intval($matches['cacheControl']);
                    break;
                }
            }
        }

        if($response->hasHeader('age')) {
            $cacheTimeInSec -= intval($response->getHeader('age')[0]);
        }

        return max(0, $cacheTimeInSec);
    }

    /**
     * @return int
     */
    public function getExpirationTimeMilliseconds(): int
    {
        return $this->expirationTimeMilliseconds;
    }

    /**
     * @return string
     */
    public function getPublicCertsEncodedUrl(): string
    {
        return $this->publicCertsEncodedUrl;
    }
}