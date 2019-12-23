<?php


namespace Firebase\Auth\Internal;


use Firebase\Util\Error\AuthClientErrorCode;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAuthError;
use Firebase\Util\Validator\Validator;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class IAMSigner implements CryptoSigner
{
    private const IAM_SIGN_BLOB_URL = 'https://iam.googleapis.com/v1/projects/-/serviceAccounts/%s:signBlob';

    /**
     * @var string|null
     */
    private $serviceAccount;

    public function __construct(string $serviceAccount)
    {
        Validator::isNonEmptyString($serviceAccount);
        $this->serviceAccount = $serviceAccount;
    }

    public function sign(string $payload)
    {
        $encodedUrl = sprintf(self::IAM_SIGN_BLOB_URL, $this->serviceAccount);
        $encodedPayload = base64_encode($payload);
        $content = [
            'bytesToSign' => $encodedPayload
        ];

        $request = new Request('POST', $encodedUrl, [], $content);
        $response = (new Client())->send($request);
        $body = json_decode($response->getBody(), true);
        return base64_decode($body['signature']);
    }

    public function getAccount(): string
    {
        return $this->serviceAccount;
    }
}
