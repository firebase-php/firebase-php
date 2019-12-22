<?php


namespace Firebase\Auth\Credential;


use Firebase\Auth\Credential;

class ApplicationDefaultCredential implements FirebaseCredential
{
    /**
     * @var Credential
     */
    private $credential;

    public function __construct()
    {
        if(getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
            $this->credential = CredentialHelpers::credentialFromFile(getenv('GOOGLE_APPLICATION_CREDENTIALS'));
            return;
        }

        // TODO: Check GCLOUD_CREDENTIAL_PATH

        $this->credential = new MetadataServiceCredential();
    }

    /**
     * @return GoogleOAuthAccessToken
     */
    public function getAccessToken(): GoogleOAuthAccessToken
    {
        return $this->credential->getAccessToken();
    }

    /**
     * @return Certificate|null
     */
    public function getCertificate(): ?Certificate
    {
        return CredentialHelpers::tryGetCertificate($this->credential);
    }

    /**
     * @return Credential
     */
    public function getCredential(): Credential
    {
        return $this->credential;
    }
}
