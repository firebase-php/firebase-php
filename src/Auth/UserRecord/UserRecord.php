<?php


namespace Firebase\Auth\UserRecord;


use Firebase\Util\Error\AuthClientErrorCode;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAuthError;

class UserRecord
{
    private $uid;

    private $email;

    private $emailVerified;

    private $displayName;

    private $photoURL;

    private $phoneNumber;

    /**
     * @var bool
     */
    private $disabled;

    /**
     * @var UserMetadata
     */
    private $metadata;

    /**
     * @var UserInfo[]
     */
    private $providerData;

    private $passwordHash;

    private $passwordSalt;

    private $customClaims;

    private $tenantId;

    private $tokensValidAfterTime;

    public function __construct(array $response)
    {
        if(!isset($response['localId'])) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INTERNAL_ERROR),
                'INTERNAL ASSERT FAILED: Invalid user response'
            );
        }

        $this->uid = $response['localId'];
        $this->email = $response['email'];
        $this->emailVerified = $response['emailVerified'];
        $this->displayName = $response['displayName'];
        $this->photoURL = $response['photoURL'];
        $this->phoneNumber = $response['phoneNumber'];

        // If disabled is not provided, the account is enabled by default.
        $this->disabled = $response['disabled'] ?? false;
        $this->metadata = new UserMetadata($response);
        $providerData = [];
        $providerUserInfo = isset($response['providerUserInfo']) ?? [];

        foreach($providerUserInfo as $entry) {
            $providerData[] = new UserInfo($entry);
        }

        $this->providerData = $providerData;

        // If the password hash is redacted (probably due to missing permissions)
        // then clear it out, similar to how the salt is returned. (Otherwise, it
        // *looks* like a b64-encoded hash is present, which is confusing.)
        if($response['passwordHash'] === B64_REDACTED) {
            $this->passwordHash = null;
        } else {
            $this->passwordHash = $response['passwordHash'];
        }

        $this->passwordSalt = $response['passwordSalt'];
        $this->customClaims = json_decode($response['customAttributes'], true);

        /** @var string | null $validAfterTime */
        $validAfterTime = null;

        if(isset($response['validSince'])) {
            $validAfterTime = UserRecordHelper::parseDate($response['validSince'] * 1000);
        }

        $this->tokensValidAfterTime = $validAfterTime;
        $this->tenantId = $response['tenantId'];
    }

    public function toArray() {
        $array = [
            'uid' => $this->uid,
            'email' => $this->email,
            'emailVerified' => $this->emailVerified,
            'displayName' => $this->displayName,
            'photoURL' => $this->photoURL,
            'phoneNumber' => $this->phoneNumber,
            'disabled' => $this->disabled,
            'metadata' => $this->metadata->toArray(),
            'passwordHash' => $this->passwordHash,
            'passwordSalt' => $this->passwordSalt,
            'customClaims' => $this->customClaims,
            'tokensValidAfterTime' => $this->tokensValidAfterTime,
            'tenantId' => $this->tenantId,
            'providerData' => []
        ];
        foreach ($this->providerData as $entry) {
            $array['providerData'][] = $entry->toArray();
        }

        return $array;
    }
}
