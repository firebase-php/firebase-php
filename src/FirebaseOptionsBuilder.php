<?php


namespace Firebase;

use Firebase\Util\Validator\Validator;
use Firebase\Auth\GoogleAuthLibrary\Credentials\ServiceAccountCredentials;
use GuzzleHttp\ClientInterface;

class FirebaseOptionsBuilder
{
    /**
     * @var array
     */
    private $databaseAuthVariableOverride = [];

    /**
     * @var string
     */
    private $databaseUrl;

    /**
     * @var string
     */
    private $projectId;

    /**
     * @var string
     */
    private $storageBucket;

    /**
     * @var string
     */
    private $serviceAccountId;

    /**
     * @var ServiceAccountCredentials|mixed
     */
    private $credentials;

    /**
     * @var int
     */
    private $connectTimeout;

    /**
     * @var int
     */
    private $readTimeout;

    /**
     * @var ClientInterface
     */
    private $httpClient;


    public function __construct(FirebaseOptions $options = null) {
        if(is_null($options)) {
            return;
        }
        $this->databaseUrl = $options->getDatabaseUrl();
        $this->storageBucket = $options->getStorageBucket();
        $this->credentials = $options->getCredentials();
        $this->databaseAuthVariableOverride = $options->getDatabaseAuthVariableOverride();
        $this->projectId = $options->getProjectId();
        $this->connectTimeout = $options->getConnectTimeout();
        $this->readTimeout = $options->getReadTimeout();
    }

    /**
     * @return array
     */
    public function getDatabaseAuthVariableOverride(): ?array
    {
        return $this->databaseAuthVariableOverride;
    }

    /**
     * @param array $databaseAuthVariableOverride
     * @return FirebaseOptionsBuilder
     */
    public function setDatabaseAuthVariableOverride(?array $databaseAuthVariableOverride): FirebaseOptionsBuilder
    {
        $this->databaseAuthVariableOverride = $databaseAuthVariableOverride;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDatabaseUrl(): ?string
    {
        return $this->databaseUrl;
    }

    /**
     * @param string $databaseUrl
     * @return FirebaseOptionsBuilder
     */
    public function setDatabaseUrl(?string $databaseUrl): FirebaseOptionsBuilder
    {
        $this->databaseUrl = $databaseUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getProjectId(): ?string
    {
        return $this->projectId;
    }

    /**
     * @param string $projectId
     * @return FirebaseOptionsBuilder
     */
    public function setProjectId(?string $projectId): FirebaseOptionsBuilder
    {
        Validator::isNonEmptyString($projectId, 'Project ID must not be null or empty');
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * @return string
     */
    public function getStorageBucket(): ?string
    {
        return $this->storageBucket;
    }

    /**
     * @param string $storageBucket
     * @return FirebaseOptionsBuilder
     */
    public function setStorageBucket(?string $storageBucket): FirebaseOptionsBuilder
    {
        Validator::isNonEmptyString($storageBucket, 'Storage bucket must not be null or empty');
        $this->storageBucket = $storageBucket;
        return $this;
    }

    /**
     * @return string
     */
    public function getServiceAccountId(): ?string
    {
        return $this->serviceAccountId;
    }

    /**
     * @param string $serviceAccountId
     * @return FirebaseOptionsBuilder
     */
    public function setServiceAccountId(?string $serviceAccountId): FirebaseOptionsBuilder
    {
        Validator::isNonEmptyString($serviceAccountId, 'Service account ID must not be null or empty');
        $this->serviceAccountId = $serviceAccountId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @param ServiceAccountCredentials|mixed $credentials
     * @return FirebaseOptionsBuilder
     */
    public function setCredentials($credentials)
    {
        Validator::isNonNullObject($credentials);
        $this->credentials = $credentials;
        return $this;
    }

    /**
     * @return int
     */
    public function getConnectTimeout(): ?int
    {
        return $this->connectTimeout;
    }

    /**
     * @param int $connectTimeout
     * @return FirebaseOptionsBuilder
     */
    public function setConnectTimeout(int $connectTimeout): FirebaseOptionsBuilder
    {
        $this->connectTimeout = $connectTimeout;
        return $this;
    }

    /**
     * @return int
     */
    public function getReadTimeout(): ?int
    {
        return $this->readTimeout;
    }

    /**
     * @param int $readTimeout
     * @return FirebaseOptionsBuilder
     */
    public function setReadTimeout(int $readTimeout): FirebaseOptionsBuilder
    {
        $this->readTimeout = $readTimeout;
        return $this;
    }

    /**
     * @return ClientInterface
     */
    public function getHttpClient(): ?ClientInterface
    {
        return $this->httpClient;
    }

    /**
     * @param ClientInterface $httpClient
     * @return FirebaseOptionsBuilder
     */
    public function setHttpClient(ClientInterface $httpClient): FirebaseOptionsBuilder
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    public function build() {
        return new FirebaseOptions($this);
    }
}
