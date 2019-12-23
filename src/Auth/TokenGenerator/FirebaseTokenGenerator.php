<?php


namespace Firebase\Auth\TokenGenerator;


use Carbon\Carbon;
use Firebase\Util\Error\AuthClientErrorCode;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAuthError;
use Firebase\Util\Validator\Validator;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class FirebaseTokenGenerator
{
    private const FIREBASE_AUDIENCE = 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit';

    private const BLACKLISTED_CLAIMS = ['acr', 'amr', 'at_hash', 'aud', 'auth_time', 'azp', 'cnf', 'c_hash', 'exp', 'iat', 'iss', 'jti', 'nbf', 'nonce'];

    /**
     * @var CryptoSigner
     */
    private $signer;

    /**
     * @var string|null
     */
    private $tenantId;

    public function __construct(CryptoSigner $signer, string $tenantId = null) {
        if(!Validator::isNonNullObject($signer)) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_CREDENTIAL['code']),
                'INTERNAL ASSERT: Must provide a CryptoSigner to use FirebaseTokenGenerator.'
            );
        }

        // TODO: TenantID

        $this->signer = $signer;
    }

    public function createCustomToken(string $uid, array $developerClaims = null): string {
        $errorMessage = null;
        if(!Validator::isNonEmptyString($uid)) {
            $errorMessage = '`uid` argument must be a non-empty string uid.';
        } elseif (strlen($uid) > 128) {
            $errorMessage = '`uid` argument must a uid with less than or equal to 128 characters.';
        }

        if($errorMessage) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
                $errorMessage
            );
        }

        $now = intval(Carbon::now()->timestamp);
        $serviceAccountEmail = $this->signer->getAccountId();
        $header = [
            'alg' => (new Sha256())->getAlgorithmId(),
            'typ' => 'JWT'
        ];
        $payload = [
            'iss' => $serviceAccountEmail,
            'sub' => $serviceAccountEmail,
            'aud' => self::FIREBASE_AUDIENCE,
            'uid' => $uid,
            'iat' => $now,
            'exp' => $now + 60*60
        ];
        $claims = [];
        if(is_array($developerClaims)) {
            foreach (array_keys($developerClaims) as $key) {
                if(in_array($key, self::BLACKLISTED_CLAIMS)) {
                    throw new FirebaseAuthError(
                        new ErrorInfo(AuthClientErrorCode::INVALID_ARGUMENT['code']),
                        "Developer claim $key is reserved and cannot be specified."
                    );
                }
                $claims[$key] = $developerClaims[$key];
            }
        }
        if(count($claims) > 0) {
            $payload['claims'] = $claims;
        }
        $headerString = base64_encode(json_encode($header));
        $payloadString = base64_encode(json_encode($payload));
        $content = $headerString . '.' . $payloadString;
        $signature = $this->signer->sign($content);
        return $content . '.' . $signature;
    }
}
