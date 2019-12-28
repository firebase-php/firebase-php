<?php


namespace Firebase\Auth;

use Firebase\Auth\Internal\GetAccountInfoResponse\User;
use Firebase\Auth\UserRecord\UpdateRequest;
use Firebase\Util\Validator\Validator;

class UserRecord implements UserInfo
{
    public const PROVIDER_ID = 'firebase';

    public const REMOVABLE_FIELDS = [
        'displayName' => 'DISPLAY_NAME',
        'photoUrl' => 'PHOTO_URL'
    ];

    public const CUSTOM_ATTRIBUTES = 'customAttributes';

    public const MAX_CLAIMS_PAYLOAD_SIZE = 1000;

    /**
     * @var string|null
     */
    private $uid;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var bool
     */
    private $emailVerified = false;

    /**
     * @var string|null
     */
    private $displayName;

    /**
     * @var string|null
     */
    private $photoUrl;

    /**
     * @var string|null
     */
    private $phoneNumber;

    /**
     * @var bool
     */
    private $disabled;

    /**
     * @var UserProvider[]
     */
    private $providers;

    /**
     * @var string|null
     */
    private $passwordHash;

    /**
     * @var string|null
     */
    private $passwordSalt;

    /**
     * @var array
     */
    private $customClaims;

    /**
     * @var string|null
     */
    private $tenantId;

    /**
     * @var int
     */
    private $tokensValidAfterTimestamp;

    /**
     * @var UserMetadata
     */
    private $userMetadata;

    public function __construct(User $response = null)
    {
        Validator::isNonNullObject($response, 'Response must not be null');
        Validator::isNonEmptyString($response->getUid());
        $this->uid = $response->getUid();
        $this->email = $response->getEmail();
        $this->phoneNumber = $response->getPhoneNumber();
        $this->emailVerified = $response->isEmailVerified();
        $this->displayName = $response->getDisplayName();
        $this->photoUrl = $response->getPhotoUrl();
        $this->disabled = $response->isDisabled();
        if(empty($response->getProviders())) {
            $this->providers = [];
        } else {
            foreach ($response->getProviders() as $provider) {
                $this->providers[] = new ProviderUserInfo($provider);
            }
        }
        $this->tokensValidAfterTimestamp = $response->getValidSince() * 1000;
        $this->userMetadata = new UserMetadata($response->getCreatedAt(), $response->getLastLoginAt());
        $this->customClaims = $this->parseCustomClaims($response->getCustomClaims());
    }

    public static function checkCustomClaims(array $customClaims = null) {
        if(!is_array($customClaims)) {
            return;
        }

        foreach($customClaims as $key => $claim) {
            Validator::isNonEmptyString($key, 'Claim names must not be null or empty');
            Validator::checkArgument(!in_array($key, FirebaseUserManager::RESERVED_CLAIMS), "Claim $key is reserved and cannot be set");
        }
    }

    public static function checkValidSince(int $epochSeconds) {
        Validator::checkArgument($epochSeconds > 0, "validSince (seconds since epoch) must be greater than 0: $epochSeconds");
    }

    public static function serializeCustomClaims(array $customClaims = null): string {
        if(is_null($customClaims) || empty($customClaims)) {
            return json_encode([]);
        }

        $claimPayloads = json_encode($customClaims);
        Validator::checkArgument(strlen($claimPayloads) <= self::MAX_CLAIMS_PAYLOAD_SIZE,
            sprintf('Custom claims payload cannot be larger than %d characters', self::MAX_CLAIMS_PAYLOAD_SIZE));

        return $claimPayloads;
    }

    private function parseCustomClaims(string $customClaims = null) {
        if(empty($customClaims)) {
            return [];
        }

        return json_decode($customClaims, true);
    }

    /**
     * @return string|null
     */
    public function getUid(): ?string
    {
        return $this->uid;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function isEmailVerified(): ?bool
    {
        return $this->emailVerified;
    }

    /**
     * @return string|null
     */
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * @return string|null
     */
    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @return bool
     */
    public function isDisabled(): ?bool
    {
        return $this->disabled;
    }

    /**
     * @return UserInfo[]
     */
    public function getProviderData(): ?array
    {
        return $this->providers;
    }

    /**
     * @return UserMetadata
     */
    public function getUserMetadata(): ?UserMetadata
    {
        return $this->userMetadata;
    }

    /**
     * @return array
     */
    public function getCustomClaims(): ?array
    {
        return $this->customClaims;
    }

    /**
     * @return string|null
     */
    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }

    /**
     * @return int
     */
    public function getTokensValidAfterTimestamp(): ?int
    {
        return $this->tokensValidAfterTimestamp;
    }

    public function getProviderId(): ?string
    {
        return self::PROVIDER_ID;
    }

    public function updateRequest() {
        return new UpdateRequest($this->uid);
    }
}
