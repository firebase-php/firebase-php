<?php


namespace Firebase;

use Firebase\Util\Validator\Validator;
use Google\Auth\ApplicationDefaultCredentials;
use Google\Auth\SignBlobInterface;

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
     * @var array|null
     */
    private $databaseAuthVariableOverride;

    /**
     * @var string|null
     */
    private $serviceAccountId;

    /**
     * @var string|null
     */
    private $projectId;

    /**
     * @var array|null
     */
    private $httpMiddlewares;

    /**
     * @var array|null
     */
    private $httpConfigs;

    /**
     * @var SignBlobInterface|null
     */
    private $credentials;

    public function __construct(FirebaseOptionsBuilder $builder)
    {
        $this->databaseUrl = $builder->getDatabaseUrl();
        $this->databaseAuthVariableOverride = $builder->getDatabaseAuthVariableOverride();
        $this->projectId = $builder->getProjectId();
        if (!empty($builder->getStorageBucket())) {
            preg_match('/^gs:\\/\\//', $builder->getStorageBucket(), $matches);
            Validator::checkArgument(empty($matches), 'StorageBucket must not include "gs://" prefix.');
        }
        $this->storageBucket = $builder->getStorageBucket();
        $this->credentials = Validator::isNonNullObject($builder->getCredentials(), 'FirebaseOptions must be initialized with setCredentials().');
        $this->httpMiddlewares = $builder->getHttpMiddlewares() ?? [];
        $this->httpConfigs = $builder->getHttpConfigs() ?? [];
    }

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
     * @return SignBlobInterface|null
     */
    public function getCredentials()
    {
        return $this->validateCredentials($this->credentials);
    }

    /**
     * @param SignBlobInterface|mixed $credentials
     * @return FirebaseOptions
     */
    public function setCredentials($credentials)
    {
        Validator::isNonNullObject(
            $credentials,
            'FirebaseOptions must be initialized with setCredentials().'
        );
        $this->credentials = $this->validateCredentials($credentials);
        return $this;
    }

    private function validateCredentials($credentials)
    {
        return Validator::isNonNullObject(
            $credentials,
            'FirebaseOptions must be initialized with setCredentials().'
        );
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

    /**
     * @return array|null
     */
    public function getHttpMiddlewares(): ?array
    {
        return $this->httpMiddlewares;
    }

    /**
     * @param array|null $httpMiddlewares
     * @return FirebaseOptions
     */
    public function setHttpMiddlewares(?array $httpMiddlewares): FirebaseOptions
    {
        $this->httpMiddlewares = $httpMiddlewares;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getHttpConfigs(): ?array
    {
        return $this->httpConfigs;
    }

    /**
     * @param array|null $httpConfigs
     * @return FirebaseOptions
     */
    public function setHttpConfigs(?array $httpConfigs): FirebaseOptions
    {
        $this->httpConfigs = $httpConfigs;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getServiceAccountId(): ?string
    {
        return $this->serviceAccountId;
    }

    /**
     * @param string|null $serviceAccountId
     * @return FirebaseOptions
     */
    public function setServiceAccountId(?string $serviceAccountId): FirebaseOptions
    {
        $this->serviceAccountId = $serviceAccountId;
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

    public static function getDefaultApplicationCredentials()
    {
        return ApplicationDefaultCredentials::getCredentials(self::FIREBASE_SCOPES);
    }

    public static function builder()
    {
        return new FirebaseOptionsBuilder(null);
    }
}
