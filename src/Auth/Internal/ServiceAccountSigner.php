<?php


namespace Firebase\Auth\Internal;


use Firebase\Auth\Credential\Certificate;
use Firebase\Util\Error\AuthClientErrorCode;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAuthError;
use Firebase\Util\Validator\Validator;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signature;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class ServiceAccountSigner implements CryptoSigner
{
    /**
     * @var Certificate
     */
    private $certificate;

    public function __construct(Certificate $certificate)
    {
        if(!$certificate) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_CREDENTIAL['code']),
                'INTERNAL ASSERT: Must provide a certificate to initialize ServiceAccountSigner.'
            );
        }
        if(!Validator::isNonEmptyString($certificate->getClientEmail()) || !Validator::isNonEmptyString($certificate->getPrivateKey())) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_CREDENTIAL['code']),
                'INTERNAL ASSERT: Must provide a certificate with validate clientEmail and privateKey to initialize ServiceAccountSigner.'
            );
        }
        $this->certificate = $certificate;
    }

    public function sign(string $payload)
    {
        $sign = new Sha256();
        return $sign->createHash($payload, new Key($this->certificate->getPrivateKey()));
    }

    public function getAccount(): string
    {
        return $this->certificate->getClientEmail();
    }
}
