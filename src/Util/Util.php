<?php


namespace Firebase\Util;


use Firebase\Auth\Credential\Certificate;
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
        try {
            Validator::isNonEmptyString($options->getProjectId());
            return $options->getProjectId();
        } catch (\Exception $e) {
        }

        try {
            $cert = CredentialHelpers::tryGetCertificate($options->getCredential());
            Validator::isNonEmptyString($cert->getProjectId());
            return $cert->getProjectId();
        } catch (\Exception $e) {
        }

        try {
            $projectId = getenv('GOOGLE_CLOUD_PROJECT') || getenv('GCLOUD_PROJECT');
            Validator::isNonEmptyString($projectId);
            return $projectId;
        } catch (\Exception $e) {
        }

        return null;
    }
}