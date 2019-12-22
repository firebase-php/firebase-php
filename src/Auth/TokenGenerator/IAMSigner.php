<?php


namespace Firebase\Auth\TokenGenerator;


use Firebase\Util\Error\AuthClientErrorCode;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAuthError;
use Firebase\Util\Validator\Validator;

class IAMSigner implements CryptoSigner
{
    /**
     * @var string|null
     */
    private $serviceAccountId;

    public function __construct(string $serviceAccountId)
    {
        if(!Validator::isNonEmptyString($serviceAccountId)) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
                'INTERNAL ASSERT: Service account ID must be undefined or a non-empty string.'
            );
        }
        $this->serviceAccountId = $serviceAccountId;
    }

    public function sign(string $payload)
    {

    }

    public function getAccountId(): string
    {
        if(Validator::isNonEmptyString($this->serviceAccountId)) {
            return $this->serviceAccountId;
        }
    }

}