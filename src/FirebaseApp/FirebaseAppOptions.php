<?php

namespace Firebase\FirebaseApp;

use Firebase\Auth\Credential;

class FirebaseAppOptions {
    /**
     * @var Credential|null
     */
    private $credential;

    /**
     * @var array|null
     */
    private $databaseAuthVariableOverride;

    /**
     * @var string|null
     */
    private $databaseUrl;

    /**
     * @var string|null
     */
    private $serviceAccountId;

    /**
     * @var string|null
     */
    private $storageBucket;

    /**
     * @var string|null
     */
    private $projectId;

    /**
     * @return Credential|null
     */
    public function getCredential(): ?Credential
    {
        return $this->credential;
    }

    /**
     * @param Credential|null $credential
     * @return FirebaseAppOptions
     */
    public function setCredential(?Credential $credential): FirebaseAppOptions
    {
        $this->credential = $credential;
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
     * @return FirebaseAppOptions
     */
    public function setDatabaseAuthVariableOverride(?array $databaseAuthVariableOverride): FirebaseAppOptions
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
     * @param string|null $databaseUrl
     * @return FirebaseAppOptions
     */
    public function setDatabaseUrl(?string $databaseUrl): FirebaseAppOptions
    {
        $this->databaseUrl = $databaseUrl;
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
     * @return FirebaseAppOptions
     */
    public function setServiceAccountId(?string $serviceAccountId): FirebaseAppOptions
    {
        $this->serviceAccountId = $serviceAccountId;
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
     * @return FirebaseAppOptions
     */
    public function setStorageBucket(?string $storageBucket): FirebaseAppOptions
    {
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
     * @return FirebaseAppOptions
     */
    public function setProjectId(?string $projectId): FirebaseAppOptions
    {
        $this->projectId = $projectId;
        return $this;
    }
}
