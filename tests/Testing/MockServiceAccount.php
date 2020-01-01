<?php


namespace Firebase\Tests\Testing;

use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Firebase\Auth\ServiceAccount;

class MockServiceAccount
{
    /**
     * @var ServiceAccount|null
     */
    private $serviceAccount;

    /**
     * @var string|null
     */
    private $cert;

    /**
     * @var string|null
     */
    private $email;

    public static function OWNER()
    {
        $json = TestUtils::loadResource('/service_accounts/owner.json');
        $cert = TestUtils::loadResource('/service_accounts/owner_public_key.pem');
        $email = 'mock-project-id-owner@mock-project-id.iam.gserviceaccount.com';

        return new MockServiceAccount($email, $json, $cert);
    }

    public static function EDITOR()
    {
        $json = TestUtils::loadResource('/service_accounts/editor.json');
        $cert = TestUtils::loadResource('/service_accounts/editor_public_key.pem');
        $email = 'mock-project-id-editor@mock-project-id.iam.gserviceaccount.com';

        return new MockServiceAccount($email, $json, $cert);
    }

    public static function VIEWER()
    {
        $json = TestUtils::loadResource('/service_accounts/viewer.json');
        $cert = TestUtils::loadResource('/service_accounts/viewer_public_key.pem');
        $email = 'mock-project-id-viewer@mock-project-id.iam.gserviceaccount.com';

        return new MockServiceAccount($email, $json, $cert);
    }

    public static function NONE()
    {
        $json = TestUtils::loadResource('/service_accounts/none.json');
        $cert = TestUtils::loadResource('/service_accounts/none_public_key.pem');
        $email = 'mock-project-id-none@mock-project-id.iam.gserviceaccount.com';

        return new MockServiceAccount($email, $json, $cert);
    }

    public static function EMPTY()
    {
        $json = TestUtils::loadResource('/service_accounts/empty.json');
        $cert = TestUtils::loadResource('/service_accounts/empty_public_key.pem');
        $email = 'mock-project-id-empty@mock-project-id.iam.gserviceaccount.com';

        return new MockServiceAccount($email, $json, $cert);
    }

    public function __construct(string $email, string $json, string $cert)
    {
        $this->serviceAccount = new ServiceAccount($json);
        $this->cert = $cert;
        $this->email = $email;
    }

    public function getPrivateKey()
    {
        return $this->serviceAccount->getPrivateKey();
    }

    /**
     * @return ServiceAccount|null
     */
    public function getServiceAccount(): ?ServiceAccount
    {
        return $this->serviceAccount;
    }

    /**
     * @return string
     */
    public function getCert(): string
    {
        return $this->cert;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    public function verifySignature(Token $token)
    {
        $publicKey = openssl_get_publickey($this->cert);
        $keyData = openssl_pkey_get_details($publicKey);
        return $token->verify(new Sha256(), $keyData['key']);
    }
}
