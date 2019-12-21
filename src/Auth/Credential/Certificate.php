<?php


namespace Firebase\Auth\Credential;


use Firebase\Util\Error\AppErrorCodes;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAppError;
use Firebase\Util\Validator\Validator;

class Certificate
{
    /**
     * @var string
     */
    private $projectId;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var string
     */
    private $clientEmail;

    public static function fromPath(string $filePath): Certificate {
        $jsonString = file_get_contents($filePath);
        try {
            $data = json_decode($jsonString);
            return new Certificate($data);
        } catch (\Exception $e) {
            throw new FirebaseAppError(
                new ErrorInfo(AppErrorCodes::INVALID_CREDENTIAL),
                'Failed to parse certificate key file'
            );
        }
    }

    public function __construct(array $json)
    {
        $this->projectId = $json['project_id'];
        $this->privateKey = $json['private_key'];
        $this->clientEmail = $json['client_email'];
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
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * @return string
     */
    public function getClientEmail(): string
    {
        return $this->clientEmail;
    }
}
