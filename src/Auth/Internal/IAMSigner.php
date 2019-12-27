<?php


namespace Firebase\Auth\Internal;

use Firebase\Util\Validator\Validator;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;

class IAMSigner implements CryptoSigner
{
    private const IAM_SIGN_BLOB_URL = 'https://iam.googleapis.com/v1/projects/-/serviceAccounts/%s:signBlob';

    /**
     * @var string|null
     */
    private $serviceAccount;

    private $httpClient;

    private $signEndpoint;

    public function __construct(string $serviceAccount, ?ClientInterface $httpClient = null)
    {
        Validator::isNonEmptyString($serviceAccount);
        $this->serviceAccount = $serviceAccount;
        if(is_null($httpClient)) {
            $this->httpClient = new Client();
        } else {
            $this->httpClient = $httpClient;
        }
    }

    public function sign(string $payload)
    {
        $this->signEndpoint = sprintf(self::IAM_SIGN_BLOB_URL, $this->serviceAccount);
        $encodedPayload = base64_encode($payload);
        $content = [
            'bytesToSign' => $encodedPayload
        ];

        $request = new Request('POST', $this->signEndpoint, [], json_encode($content));
        $response = $this->httpClient->send($request);
        $body = json_decode($response->getBody(), true);
        return base64_decode($body['signature']);
    }

    public function getAccount(): string
    {
        return $this->serviceAccount;
    }

    /**
     * @return mixed
     */
    public function getSignEndpoint()
    {
        return $this->signEndpoint;
    }
}
