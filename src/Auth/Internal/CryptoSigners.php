<?php


namespace Firebase\Auth\Internal;


use Firebase\Auth\Credential\CredentialHelpers;
use Firebase\FirebaseApp;
use Firebase\Util\Validator\Validator;

class CryptoSigners
{
    private const METADATA_SERVICE_URL = 'http://metadata/computeMetadata/v1/instance/service-accounts/default/email';

    public static function getCryptoSigner(FirebaseApp $firebaseApp) {
        if($firebaseApp->getOptions()->getCredential()) {
            $cert = CredentialHelpers::tryGetCertificate($firebaseApp->getOptions()->getCredential());
            try {
                Validator::isNonNullObject($cert);
                Validator::isNonEmptyString($cert->getPrivateKey());
                Validator::isNonEmptyString($cert->getClientEmail());
                return new ServiceAccountSigner($cert);
            } catch (\Exception $e) {}
        }

        return new IAMSigner($firebaseApp->getOptions()->getServiceAccountId());
    }
}
