<?php


namespace Firebase\Tests\Testing;


use Carbon\Carbon;
use Firebase\JWT\JWT;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class TestTokenFactory
{
    public const PROJECT_ID = 'proj-test-101';

    public const PRIVATE_KEY_ID = 'aaaaaaaaaabbbbbbbbbbccccccccccdddddddddd';

    public const UID = 'someUid';

    private $privateKey;

    private $issuer;

    public function __construct(?string $privateKey = null, ?string $issuer = null)
    {
        $this->privateKey = $privateKey;
        $this->issuer = $issuer;
    }

    public function createToken(?array $header = null, ?array $payload = null, ?Signer $signer = null) {
        if(is_null($header)) {
            $header = $this->createHeader();
        }
        if(is_null($payload)) {
            $payload = $this->createTokenPayload();
        }
        if(is_null($signer)) {
            $signer = new Sha256();
        }
        return (new Builder())
            ->issuedBy($payload['iss'] ?? null)
            ->issuedAt($payload['iat'] ?? null)
            ->permittedFor($payload['aud'] ?? null)
            ->expiresAt($payload['exp'] ?? null)
            ->relatedTo($payload['sub'] ?? null)
            ->withHeader('kid', $header['kid'] ?? null)
            ->getToken($signer, new Key($this->privateKey));
    }

    public function createHeader() {
        return [
            'alg' => (new Sha256())->getAlgorithmId(),
            'type' => 'JWT',
            'kid' => self::PRIVATE_KEY_ID
        ];
    }

    public function createTokenPayload() {
        $now = Carbon::now()->timestamp;
        return [
            'iss' => $this->issuer,
            'aud' => self::PROJECT_ID,
            'iat' => $now,
            'exp' => $now + 3600,
            'sub' => self::UID
        ];
    }
}
