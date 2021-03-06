<?php


namespace Firebase\Auth\Internal;

use Carbon\Carbon;
use Firebase\Util\Validator\Validator;
use Google\Auth\SignBlobInterface;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class FirebaseTokenFactory
{
    const FIREBASE_AUDIENCE = 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit';

    const TOKEN_DURATION_SECONDS = 3600;

    /**
     * @var SignBlobInterface
     */
    private $signer;

    public function __construct(SignBlobInterface $signer)
    {
        $this->signer = $signer;
    }

    public function createSignedCustomAuthTokenForUser(?string $uid, ?array $developerClaims = null)
    {
        Validator::isUid($uid);

        $header = [
            'alg' => (new Sha256())->getAlgorithmId(),
            'typ' => 'JWT'
        ];
        $iat = intval(Carbon::now()->timestamp);
        $payload = [
            'uid' => $uid,
            'iss' => $this->signer->getClientName(),
            'sub' => $this->signer->getClientName(),
            'aud' => self::FIREBASE_AUDIENCE,
            'iat' => $iat,
            'exp' => $iat + self::TOKEN_DURATION_SECONDS
        ];

        if (!is_null($developerClaims)) {
            $reservedNames = array_keys($payload);
            foreach ($developerClaims as $key => $claim) {
                Validator::checkArgument(!in_array($key, $reservedNames), sprintf('developerClaims must not contain a reserved key: %s', $key));
            }
            if (!empty($developerClaims)) {
                $payload['claims'] = $developerClaims;
            }
        }

        return $this->signPayload($header, $payload);
    }

    private function signPayload(array $header = [], array $payload = [])
    {
        $headerString = base64_encode(json_encode($header));
        $payloadString = base64_encode(json_encode($payload));
        $content = sprintf('%s.%s', $headerString, $payloadString);
        $signature = $this->signer->signBlob($content);
        return sprintf('%s.%s', $content, $signature);
    }
}
