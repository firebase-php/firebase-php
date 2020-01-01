<?php


namespace Firebase;

use Firebase\Auth\ServiceAccount;
use Firebase\Util\Validator\Validator;
use Google\Auth\ApplicationDefaultCredentials;

final class FirebaseOptions
{
    public const FIREBASE_SCOPES = [
        // Enables access to Firebase Realtime Database.
        'https://www.googleapis.com/auth/firebase.database',

        // Enables access to the email address associated with a project.
        'https://www.googleapis.com/auth/userinfo.email',

        // Enables access to Google Identity Toolkit (for user management APIs).
        'https://www.googleapis.com/auth/identitytoolkit',

        // Enables access to Google Cloud Storage.
        'https://www.googleapis.com/auth/devstorage.full_control',

        // Enables access to Google Cloud Firestore
        'https://www.googleapis.com/auth/cloud-platform',
        'https://www.googleapis.com/auth/datastore'
    ];

    /**
     * @var string|null
     */
    private $databaseUrl;

    /**
     * @var string|null
     */
    private $storageBucket;

    /**
     * @var string|null
     */
    private $projectId;

    /**
     * @var ServiceAccount|null
     */
    private $serviceAccount;

    /**
     * @var array|null
     */
    private $httpClientMiddlewares;

    /**
     * @var array|null
     */
    private $httpClientConfigs;

    /**
     * @var array|null
     */
    private $databaseAuthVariableOverride;

    /**
     * @return string|null
     */
    public function getDatabaseUrl(): ?string
    {
        return $this->databaseUrl;
    }

    /**
     * @param string|null $databaseUrl
     * @return FirebaseOptions
     */
    public function setDatabaseUrl(?string $databaseUrl): FirebaseOptions
    {
        $this->databaseUrl = $databaseUrl;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStorageBucket(): ?string
    {
        return $this->storageBucket;
    }

    /**
     * @param string|null $storageBucket
     * @return FirebaseOptions
     */
    public function setStorageBucket(?string $storageBucket): FirebaseOptions
    {
        if (!empty($storageBucket)) {
            preg_match('/^gs:\\/\\//', $storageBucket, $matches);
            Validator::checkArgument(empty($matches), 'StorageBucket must not include "gs://" prefix.');
        }

        $this->storageBucket = $storageBucket;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProjectId(): ?string
    {
        return $this->projectId;
    }

    /**
     * @param string|null $projectId
     * @return FirebaseOptions
     */
    public function setProjectId(?string $projectId): FirebaseOptions
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * @return ServiceAccount|null
     */
    public function getServiceAccount(): ?ServiceAccount
    {
        Validator::isNonNullObject($this->serviceAccount, 'Service account was not set.');
        return $this->serviceAccount;
    }

    /**
     * @param ServiceAccount|null $serviceAccount
     * @return FirebaseOptions
     */
    public function setServiceAccount(?ServiceAccount $serviceAccount): FirebaseOptions
    {
        $this->serviceAccount = $serviceAccount;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getHttpClientMiddlewares(): ?array
    {
        return $this->httpClientMiddlewares;
    }

    /**
     * @param array|null $httpClientMiddlewares
     * @return FirebaseOptions
     */
    public function setHttpClientMiddlewares(?array $httpClientMiddlewares): FirebaseOptions
    {
        $middlewares = $httpClientMiddlewares ?? [];
        $middlewares[] = ApplicationDefaultCredentials::getMiddleware(self::FIREBASE_SCOPES);
        $this->httpClientMiddlewares = $middlewares;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getHttpClientConfigs(): ?array
    {
        return $this->httpClientConfigs;
    }

    /**
     * @param array|null $httpClientConfigs
     * @return FirebaseOptions
     */
    public function setHttpClientConfigs(?array $httpClientConfigs): FirebaseOptions
    {
        $this->httpClientConfigs = $httpClientConfigs ?? [];
        return $this;
    }

    /**
     * @return array|null
     */
    public function getDatabaseAuthVariableOverride(): ?array
    {
        return $this->databaseAuthVariableOverride;
    }

    /**
     * @param array|null $databaseAuthVariableOverride
     * @return FirebaseOptions
     */
    public function setDatabaseAuthVariableOverride(?array $databaseAuthVariableOverride): FirebaseOptions
    {
        $this->databaseAuthVariableOverride = $databaseAuthVariableOverride;
        return $this;
    }

    public function getCredentials()
    {
        return ApplicationDefaultCredentials::getCredentials(self::FIREBASE_SCOPES);
    }
}
