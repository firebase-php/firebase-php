<?php


namespace Firebase\Auth\UserImportBuilder;


use Firebase\Auth\UserMetadata;
use Firebase\Auth\UserProvider;

class UserImportRecordBuilder
{
    /**
     * @var string
     */
    private $uid;

    /**
     * @var string
     */
    private $email;

    /**
     * @var bool
     */
    private $emailVerified;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $photoUrl;

    /**
     * @var bool
     */
    private $disabled;

    /**
     * @var UserMetadata
     */
    private $userMetadata;

    /**
     * @var string
     */
    private $passwordHash;

    /**
     * @var string
     */
    private $passwordSalt;

    /**
     * @var UserProvider[]
     */
    private $userProviders;

    /**
     * @var array
     */
    private $customClaims;

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     * @return UserImportRecordBuilder
     */
    public function setUid(string $uid): UserImportRecordBuilder
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return UserImportRecordBuilder
     */
    public function setEmail(string $email): UserImportRecordBuilder
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    /**
     * @param bool $emailVerified
     * @return UserImportRecordBuilder
     */
    public function setEmailVerified(bool $emailVerified): UserImportRecordBuilder
    {
        $this->emailVerified = $emailVerified;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return UserImportRecordBuilder
     */
    public function setDisplayName(string $displayName): UserImportRecordBuilder
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     * @return UserImportRecordBuilder
     */
    public function setPhoneNumber(string $phoneNumber): UserImportRecordBuilder
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhotoUrl(): string
    {
        return $this->photoUrl;
    }

    /**
     * @param string $photoUrl
     * @return UserImportRecordBuilder
     */
    public function setPhotoUrl(string $photoUrl): UserImportRecordBuilder
    {
        $this->photoUrl = $photoUrl;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @param bool $disabled
     * @return UserImportRecordBuilder
     */
    public function setDisabled(bool $disabled): UserImportRecordBuilder
    {
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * @return UserMetadata
     */
    public function getUserMetadata(): UserMetadata
    {
        return $this->userMetadata;
    }

    /**
     * @param UserMetadata $userMetadata
     * @return UserImportRecordBuilder
     */
    public function setUserMetadata(UserMetadata $userMetadata): UserImportRecordBuilder
    {
        $this->userMetadata = $userMetadata;
        return $this;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @param string $passwordHash
     * @return UserImportRecordBuilder
     */
    public function setPasswordHash(string $passwordHash): UserImportRecordBuilder
    {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    /**
     * @return string
     */
    public function getPasswordSalt(): string
    {
        return $this->passwordSalt;
    }

    /**
     * @param string $passwordSalt
     * @return UserImportRecordBuilder
     */
    public function setPasswordSalt(string $passwordSalt): UserImportRecordBuilder
    {
        $this->passwordSalt = $passwordSalt;
        return $this;
    }

    /**
     * @return UserProvider[]
     */
    public function getUserProviders(): array
    {
        return $this->userProviders;
    }

    /**
     * @param UserProvider[] $userProviders
     * @return UserImportRecordBuilder
     */
    public function setUserProviders(array $userProviders): UserImportRecordBuilder
    {
        $this->userProviders = $userProviders;
        return $this;
    }

    /**
     * @return array
     */
    public function getCustomClaims(): array
    {
        return $this->customClaims;
    }

    /**
     * @param array $customClaims
     * @return UserImportRecordBuilder
     */
    public function setCustomClaims(array $customClaims): UserImportRecordBuilder
    {
        $this->customClaims = $customClaims;
        return $this;
    }

    public function build(): UserImportRecord {
        $properties = [];
        $properties['localId'] = $this->uid;
        $properties['email'] = $this->email;
        $properties['photoUrl'] = $this->photoUrl;
        $properties['phoneNumber'] = $this->phoneNumber;
        $properties['displayName'] = $this->displayName;
        $properties['createdAt'] = $this->userMetadata->getCreationTimestamp();
        $properties['lastLoginAt'] = $this->userMetadata->getLastSignInTimestamp();
        $properties['passwordHash'] = $this->passwordHash;
        $properties['salt'] = $this->passwordSalt;
        $properties['providerUserInfo'] = $this->userProviders;
        $properties['emailVerified'] = $this->emailVerified;
        $properties['disabled'] = $this->disabled;
        if(is_array($this->customClaims)) {
            $properties['customAttributes'] = $this->customClaims;
        }
        return new UserImportRecord($properties);
    }
}
