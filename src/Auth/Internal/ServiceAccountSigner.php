<?php


namespace Firebase\Auth\Internal;


use Firebase\Auth\Credential\Certificate;
use Firebase\Util\Error\AuthClientErrorCode;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAuthError;
use Firebase\Util\Validator\Validator;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signature;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class ServiceAccountSigner implements CryptoSigner
{
    private $signer;

//    public function __construct(Certificate $certificate)
//    {
//        if(!$certificate) {
//            throw new FirebaseAuthError(
//                new ErrorInfo(AuthClientErrorCode::INVALID_CREDENTIAL['code']),
//                'INTERNAL ASSERT: Must provide a certificate to initialize ServiceAccountSigner.'
//            );
//        }
//        if(!Validator::isNonEmptyString($certificate->getClientEmail()) || !Validator::isNonEmptyString($certificate->getPrivateKey())) {
//            throw new FirebaseAuthError(
//                new ErrorInfo(AuthClientErrorCode::INVALID_CREDENTIAL['code']),
//                'INTERNAL ASSERT: Must provide a certificate with validate clientEmail and privateKey to initialize ServiceAccountSigner.'
//            );
//        }
//        $this->certificate = $certificate;
//    }

    public function __construct(ServiceAccountCredentials $signer)
    {
        $this->signer = $signer;
    }

    public function sign(string $payload)
    {
        return $this->signer->signBlob($payload);
    }

    public function getAccount(): string
    {
        return $this->signer->getClientName();
    }
}
