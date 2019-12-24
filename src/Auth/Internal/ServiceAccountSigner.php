<?php


namespace Firebase\Auth\Internal;


use Google\Auth\Credentials\ServiceAccountCredentials;

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
