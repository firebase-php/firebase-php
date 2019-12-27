<?php


namespace Firebase\Auth\GoogleAuthLibrary\Credentials;


use Firebase\Auth\GoogleAuthLibrary\CredentialsLoader;

class ServiceAccountCredentials extends \Google\Auth\Credentials\ServiceAccountCredentials
{
    private $projectId;

    private $privateKeyId;

    public function __construct($scope, $jsonKey, $sub = null)
    {
        parent::__construct($scope, $jsonKey, $sub);
        $this->projectId = $jsonKey['project_id'] ?? null;
        $this->privateKeyId = $jsonKey['private_key_id'] ?? null;
    }

    /**
     * @return mixed
     */
    public function getProjectId()
    {
        if(!empty($this->projectId)) {
            return $this->projectId;
        }
        $env = self::fromEnv();
        return $env['project_id'] ?? null;
    }

    /**
     * @return mixed
     */
    public function getPrivateKeyId()
    {
        return $this->privateKeyId;
    }
}
