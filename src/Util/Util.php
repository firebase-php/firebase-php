<?php


namespace Firebase\Util;


use Firebase\Auth\Credential\CredentialHelpers;
use Firebase\FirebaseApp;
use Firebase\Util\Validator\Validator;

class Util
{
    public static function findProjectId(FirebaseApp $app): ?string {
        return self::getProjectId($app);
    }

    public static function getProjectId(FirebaseApp $app): ?string {
        $options = $app->getOptions();
        if(Validator::isNonEmptyString($options->getProjectId())) {
            return $options->getProjectId();
        }

        $cert = CredentialHelpers::tryGetCertificate($options->getCredential());
        if(!is_null($cert) && Validator::isNonEmptyString($cert->getProjectId())) {
            return $cert->getProjectId();
        }

        $projectId = getenv('GOOGLE_CLOUD_PROJECT') || getenv('GCLOUD_PROJECT');
        if(Validator::isNonEmptyString($projectId)) {
            return $projectId;
        }

        return null;
    }
}