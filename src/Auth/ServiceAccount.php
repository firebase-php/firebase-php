<?php


namespace Firebase\Auth;

use Google\Auth\CredentialsLoader;
use GuzzleHttp\Psr7\Uri;

class ServiceAccount implements \JsonSerializable
{

    /**
     * @var string|null
     */
    private $projectId;

    /**
     * @var string|null
     */
    private $privateKeyId;

    /**
     * @var string|null
     */
    private $privateKey;

    /**
     * @var string|null
     */
    private $clientEmail;

    /**
     * @var string|null
     */
    private $clientId;

    /**
     * @var Uri|null
     */
    private $authUri;

    /**
     * @var Uri|null
     */
    private $tokenUri;

    /**
     * @var Uri|null
     */
    private $authProviderX509CertUrl;

    /**
     * @var Uri|null
     */
    private $clientX509CertUrl;

    /**
     * @var array|null
     */
    private $json;

    public function __construct(?string $serviceAccount = null)
    {
        if (is_null($serviceAccount)) {
            $this->json = CredentialsLoader::fromEnv() ?? CredentialsLoader::fromWellKnownFile();
        } else {
            $this->json = json_decode($serviceAccount, true);
        }

        if (!$this->json) {
            throw new \InvalidArgumentException('Service account credentials file not found.');
        }

        $type = $this->json['type'] ?? null;
        if ($this->getType() !== $type) {
            throw new \InvalidArgumentException('Invalid credential type. Expect "service_account"');
        }
        $this->setProjectId($this->json['project_id'] ?? null);
        $this->setPrivateKeyId($this->json['private_key_id'] ?? null);
        $this->setPrivateKey($this->json['private_key'] ?? null);
        $this->setClientEmail($this->json['client_email'] ?? null);
        $this->setClientId($this->json['client_id'] ?? null);
        $this->setAuthUri($this->json['auth_uri'] ?? null);
        $this->setAuthProviderX509CertUrl($this->json['auth_provider_x509_cert_url'] ?? null);
        $this->setClientX509CertUrl($this->json['client_x509_cert_url'] ?? null);
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return 'service_account';
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
     * @return ServiceAccount
     */
    public function setProjectId(?string $projectId): ServiceAccount
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrivateKeyId(): ?string
    {
        return $this->privateKeyId;
    }

    /**
     * @param string|null $privateKeyId
     * @return ServiceAccount
     */
    public function setPrivateKeyId(?string $privateKeyId): ServiceAccount
    {
        $this->privateKeyId = $privateKeyId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    /**
     * @param string|null $privateKey
     * @return ServiceAccount
     */
    public function setPrivateKey(?string $privateKey): ServiceAccount
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getClientEmail(): ?string
    {
        return $this->clientEmail;
    }

    /**
     * @param string|null $clientEmail
     * @return ServiceAccount
     */
    public function setClientEmail(?string $clientEmail): ServiceAccount
    {
        $this->clientEmail = $clientEmail;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    /**
     * @param string|null $clientId
     * @return ServiceAccount
     */
    public function setClientId(?string $clientId): ServiceAccount
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @return Uri|null
     */
    public function getAuthUri(): ?Uri
    {
        return $this->authUri;
    }

    /**
     * @param Uri|string|null $authUri
     * @return ServiceAccount
     */
    public function setAuthUri($authUri): ServiceAccount
    {
        $this->authUri = $this->parseUri($authUri);
        return $this;
    }

    /**
     * @return Uri|null
     */
    public function getTokenUri(): ?Uri
    {
        return $this->tokenUri;
    }

    /**
     * @param Uri|string|null $tokenUri
     * @return ServiceAccount
     */
    public function setTokenUri($tokenUri): ServiceAccount
    {
        $this->tokenUri = $this->parseUri($tokenUri);
        return $this;
    }

    /**
     * @return Uri|null
     */
    public function getAuthProviderX509CertUrl(): ?Uri
    {
        return $this->authProviderX509CertUrl;
    }

    /**
     * @param Uri|string|null $authProviderX509CertUrl
     * @return ServiceAccount
     */
    public function setAuthProviderX509CertUrl($authProviderX509CertUrl): ServiceAccount
    {
        $this->authProviderX509CertUrl = $this->parseUri($authProviderX509CertUrl);
        return $this;
    }

    /**
     * @return Uri|null
     */
    public function getClientX509CertUrl(): ?Uri
    {
        return $this->clientX509CertUrl;
    }

    /**
     * @param Uri|string|null $clientX509CertUrl
     * @return ServiceAccount
     */
    public function setClientX509CertUrl($clientX509CertUrl): ServiceAccount
    {
        $this->clientX509CertUrl = $this->parseUri($clientX509CertUrl);
        return $this;
    }

    private function parseUri($uri, $errorMessage = 'Invalid URI')
    {
        if (is_null($uri)) {
            return null;
        }
        if ($uri instanceof Uri) {
            return $uri;
        }
        if (is_string($uri)) {
            return new Uri($uri);
        }

        throw new \InvalidArgumentException($errorMessage);
    }

    public function jsonSerialize()
    {
        return $this->json;
    }
}
