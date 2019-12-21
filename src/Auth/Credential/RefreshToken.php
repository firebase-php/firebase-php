<?php


namespace Firebase\Auth\Credential;


use Firebase\Util\Error\AppErrorCodes;
use Firebase\Util\Error\Error;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAppError;

class RefreshToken
{
    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var string
     */
    private $type;

    public static function fromPath(string $filePath): ?RefreshToken {
        /** @var string|null $jsonString */
        $jsonString = file_get_contents($filePath);

        try {
            return new RefreshToken(json_decode($jsonString));
        } catch (\Exception $e) {
            throw new FirebaseAppError(
                new ErrorInfo(AppErrorCodes::INVALID_CREDENTIAL),
                "Failed to parse refresh token file: $e"
            );
        }
    }

    public function __construct(array $json)
    {
        $this->clientId = $json['client_id'];
        $this->clientSecret = $json['client_secret'];
        $this->refreshToken = $json['refresh_token'];
        $this->type = $json['type'];
        $errorMessage = null;

        if(!is_string($this->clientId) || !isset($this->clientId)) {
            $errorMessage = 'Refresh token must contain a "client_id" property.';
        } else if(!is_string($this->clientSecret) || !isset($this->clientSecret)) {
            $errorMessage = 'Refresh token must contain a "client_secret" property.';
        } else if(!is_string($this->refreshToken) || !isset($this->refreshToken)) {
            $errorMessage = 'Refresh token must contain a "refresh_token" property.';
        } else if(!is_string($this->type) || !isset($this->type)) {
            $errorMessage = 'Refresh token must contain a "type" property.';
        }

        if($errorMessage) {
            throw new FirebaseAppError(
                new ErrorInfo(AppErrorCodes::INVALID_CREDENTIAL),
                $errorMessage
            );
        }
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }


}