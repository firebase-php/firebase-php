<?php


namespace Firebase;

use Firebase\Util\Validator\Validator;
use Google\Auth\ApplicationDefaultCredentials;
use Google\Auth\CredentialsLoader;
use Google\Auth\SignBlobInterface;
use GuzzleHttp\ClientInterface;

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
     * @var ClientInterface
     */
    private $httpClient;

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
        $this->httpClient = $builder->getHttpClient() ?? CredentialsLoader::makeHttpClient($this->credentials);
    }

    /**
     * @return string|null
     */
    public function getDatabaseUrl(): ?string
    {
        return $this->databaseUrl;
    }

    /**
     * @return string|null
     */
    public function getStorageBucket(): ?string
    {
        return $this->storageBucket;
    }

    /**
     * @return SignBlobInterface|null
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @return array|null
     */
    public function getDatabaseAuthVariableOverride(): ?array
    {
        return $this->databaseAuthVariableOverride;
    }

    /**
     * @return ClientInterface
     */
    public function getHttpClient(): ?ClientInterface
    {
        return $this->httpClient;
    }

    /**
     * @return string|null
     */
    public function getServiceAccountId(): ?string
    {
        return $this->serviceAccountId;
    }

    /**
     * @return string|null
     */
    public function getProjectId(): ?string
    {
        return $this->projectId;
    }

    public static function getDefaultApplicationCredentials()
    {
        return ApplicationDefaultCredentials::getCredentials(self::FIREBASE_SCOPES);
    }

    public static function builder(?FirebaseOptions $options = null)
    {
        return new FirebaseOptionsBuilder($options);
    }
}
