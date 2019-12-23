<?php


namespace Firebase;


use Google\Auth\ApplicationDefaultCredentials;
use Google\Auth\Credentials\IAMCredentials;
use Google\Auth\Credentials\ServiceAccountCredentials;

final class FirebaseOptions
{
    private const FIREBASE_SCOPES = [
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
     * @var string
     */
    private $databaseUrl;

    /**
     * @var string
     */
    private $storageBucket;

    /**
     * @var IAMCredentials|ServiceAccountCredentials
     */
    private $credentials;

    /**
     * @var array
     */
    private $databaseAuthVariableOverride;

    /**
     * @var string
     */
    private $projectId;

    /**
     * @var string
     */
    private $serviceAccountId;

    /**
     * @var int
     */
    private $connectTimeout;

    /**
     * @var int
     */
    private $readTimeout;

    /**
     * @return string
     */
    public function getDatabaseUrl(): string
    {
        return $this->databaseUrl;
    }

    /**
     * @return string
     */
    public function getStorageBucket(): string
    {
        return $this->storageBucket;
    }

    /**
     * @return IAMCredentials|ServiceAccountCredentials
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @return array
     */
    public function getDatabaseAuthVariableOverride(): array
    {
        return $this->databaseAuthVariableOverride;
    }

    /**
     * @return string
     */
    public function getProjectId(): string
    {
        return $this->projectId;
    }

    /**
     * @return string
     */
    public function getServiceAccountId(): string
    {
        return $this->serviceAccountId;
    }

    /**
     * @return int
     */
    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    /**
     * @return int
     */
    public function getReadTimeout(): int
    {
        return $this->readTimeout;
    }

    /**
     * @return \Google\Auth\Middleware\AuthTokenMiddleware
     */
    public static function getApplicationDefaultCredentials() {
        return ApplicationDefaultCredentials::getMiddleware(self::FIREBASE_SCOPES);
    }
}
