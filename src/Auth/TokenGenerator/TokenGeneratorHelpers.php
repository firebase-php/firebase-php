<?php


namespace Firebase\Auth\TokenGenerator;


use Firebase\Auth\Credential\CredentialHelpers;
use Firebase\FirebaseApp;
use Firebase\Util\Validator\Validator;

class TokenGeneratorHelpers
{
    public static function cryptoSignerFromApp(FirebaseApp $app) {
        if($app->getOptions()->getCredential()) {
            $cert = CredentialHelpers::tryGetCertificate($app->getOptions()->getCredential());
            if(!is_null($cert) && Validator::isNonEmptyString($cert->getPrivateKey()) && Validator::isNonEmptyString($cert->getClientEmail())) {
                return new ServiceAccountSigner($cert);
            }
        }

        return new IAMSigner($app->getOptions()->getServiceAccountId());
    }
}
