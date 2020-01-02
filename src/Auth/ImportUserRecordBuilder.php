<?php


namespace Firebase\Auth;

use Firebase\Util\Validator\Validator;

class ImportUserRecordBuilder
{
    /**
     * @var string|null
     */
    private $uid;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var bool|null
     */
    private $emailVerified;

    /**
     * @var string|null
     */
    private $displayName;

    /**
     * @var string|null
     */
    private $phoneNumber;

    /**
     * @var string|null
     */
    private $photoUrl;

    /**
     * @var bool|null
     */
    private $disabled;

    /**
     * @var UserMetadata|null
     */
    private $userMetadata;

    /**
     * @var string|null
     */
    private $passwordHash;

    /**
     * @var string|null
     */
    private $passwordSalt;

    /**
     * @var UserProvider[]|null
     */
    private $userProviders = [];

    /**
     * @var array
     */
    private $customClaims = [];

    /**
     * @param string|null $uid
     * @return ImportUserRecordBuilder
     */
    public function setUid(?string $uid): ImportUserRecordBuilder
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @param string|null $email
     * @return ImportUserRecordBuilder
     */
    public function setEmail(?string $email): ImportUserRecordBuilder
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param bool|null $emailVerified
     * @return ImportUserRecordBuilder
     */
    public function setEmailVerified(?bool $emailVerified): ImportUserRecordBuilder
    {
        $this->emailVerified = $emailVerified;
        return $this;
    }

    /**
     * @param string|null $displayName
     * @return ImportUserRecordBuilder
     */
    public function setDisplayName(?string $displayName): ImportUserRecordBuilder
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @param string|null $phoneNumber
     * @return ImportUserRecordBuilder
     */
    public function setPhoneNumber(?string $phoneNumber): ImportUserRecordBuilder
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @param string|null $photoUrl
     * @return ImportUserRecordBuilder
     */
    public function setPhotoUrl(?string $photoUrl): ImportUserRecordBuilder
    {
        $this->photoUrl = $photoUrl;
        return $this;
    }

    /**
     * @param bool|null $disabled
     * @return ImportUserRecordBuilder
     */
    public function setDisabled(?bool $disabled): ImportUserRecordBuilder
    {
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * @param UserMetadata|null $userMetadata
     * @return ImportUserRecordBuilder
     */
    public function setUserMetadata(?UserMetadata $userMetadata): ImportUserRecordBuilder
    {
        $this->userMetadata = $userMetadata;
        return $this;
    }

    /**
     * @param string|null $passwordHash
     * @return ImportUserRecordBuilder
     */
    public function setPasswordHash(?string $passwordHash): ImportUserRecordBuilder
    {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    /**
     * @param string|null $passwordSalt
     * @return ImportUserRecordBuilder
     */
    public function setPasswordSalt(?string $passwordSalt): ImportUserRecordBuilder
    {
        $this->passwordSalt = $passwordSalt;
        return $this;
    }

    public function addUserProvider(?UserProvider $provider): ImportUserRecordBuilder
    {
        Validator::isNonNullObject($provider);
        $this->userProviders[] = $provider;
        return $this;
    }

    public function addAllUserProviders(?array $providers = []): ImportUserRecordBuilder
    {
        $this->userProviders = array_merge($this->userProviders, $providers);
        return $this;
    }

    public function putCustomClaim(?string $key, $value): ImportUserRecordBuilder
    {
        $this->customClaims[$key] = $value;
        return $this;
    }

    public function putAllCustomClaims(?array $customClaims = []): ImportUserRecordBuilder
    {
        $this->customClaims = array_merge($this->customClaims, $customClaims);
        return $this;
    }

    public function build()
    {
        $properties = [];
        Validator::isUid($this->uid);
        $properties['localId'] = $this->uid;

        if (Validator::isNonEmptyString($this->email, '', false)) {
            Validator::isEmail($this->email);
            $properties['email'] = $this->email;
        }

        if (Validator::isNonEmptyString($this->photoUrl, '', false)) {
            Validator::isURL($this->photoUrl);
            $properties['photoUrl'] = $this->photoUrl;
        }

        if (Validator::isNonEmptyString($this->phoneNumber, '', false)) {
            Validator::isPhoneNumber($this->phoneNumber);
            $properties['phoneNumber'] = $this->phoneNumber;
        }

        if (Validator::isNonEmptyString($this->displayName, '', false)) {
            $properties['displayName'] = $this->displayName;
        }

        if (!is_null($this->userMetadata)) {
            if ($this->userMetadata->getCreationTimestamp() > 0) {
                $properties['createdAt'] = $this->userMetadata->getCreationTimestamp();
            }
            if ($this->userMetadata->getLastSignInTimestamp() > 0) {
                $properties['lastLoginAt'] = $this->userMetadata->getLastSignInTimestamp();
            }
        }

        if (!is_null($this->passwordHash)) {
            $properties['passwordHash'] = $this->passwordHash;
        }

        if (!is_null($this->passwordSalt)) {
            $properties['salt'] = $this->passwordSalt;
        }

        if (count($this->userProviders) > 0) {
            $properties['providerUserInfo'] = array_replace([], $this->userProviders);
        }

        if (count($this->customClaims) > 0) {
            $mergedClaims = array_replace([], $this->customClaims);
            UserRecord::checkCustomClaims($mergedClaims);
            $properties[UserRecord::CUSTOM_ATTRIBUTES] = $mergedClaims;
        }

        if (!is_null($this->emailVerified)) {
            $properties['emailVerified'] = $this->emailVerified;
        }

        if (!is_null($this->disabled)) {
            $properties['disabled'] = $this->disabled;
        }

        return new ImportUserRecord($properties);
    }
}
