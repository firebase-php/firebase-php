<?php


namespace Firebase\Auth\Credential;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class CertCredential implements FirebaseCredential
{
    private const GOOGLE_TOKEN_AUDIENCE = 'https://accounts.google.com/o/oauth2/token';

    private const ONE_HOUR_IN_SECONDS = 60 * 60;

    /**
     * @var Certificate
     */
    private $certificate;

    /**
     * CertCredential constructor.
     * @param string | array $serviceAccountPathOrObject
     * @throws \Firebase\Util\Error\FirebaseAppError
     */
    public function __construct($serviceAccountPathOrObject)
    {
        $this->certificate = is_string($serviceAccountPathOrObject) ? Certificate::fromPath($serviceAccountPathOrObject) : new Certificate($serviceAccountPathOrObject);
    }

    public function getAccessToken(): GoogleOAuthAccessToken
    {
        $request = new Request(
            'POST',
            self::GOOGLE_TOKEN_AUDIENCE
        );
        return CredentialHelpers::requestAccessToken($request,
            [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $this->createAuthJwt()
                ]
            ]
        );
    }

    public function getCertificate(): ?Certificate
    {
        return $this->certificate;
    }

    private function createAuthJwt(): string {
        $claims = [
            'scope' => join(' ', [
                'https://www.googleapis.com/auth/cloud-platform',
                'https://www.googleapis.com/auth/firebase.database',
                'https://www.googleapis.com/auth/firebase.messaging',
                'https://www.googleapis.com/auth/identitytoolkit',
                'https://www.googleapis.com/auth/userinfo.email',
            ])
        ];

        $signer = new Sha256();
        $privateKey = new Key($this->certificate->getPrivateKey());
        $jwtBuilder = new Builder();
        $jwtBuilder
            ->setAudience(self::GOOGLE_TOKEN_AUDIENCE)
            ->setExpiration(self::ONE_HOUR_IN_SECONDS)
            ->setIssuer($this->certificate->getClientEmail());
        foreach($claims as $key => $claim) {
            $jwtBuilder->withClaim($key, $claim);
        }

        return $jwtBuilder->getToken($signer, $privateKey);
    }

}