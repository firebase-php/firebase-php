<?php


namespace Firebase\Auth\UserRecord;


use Firebase\Util\Validator\Validator;

abstract class UserRecordRequest
{
    protected $properties = [];

    public function setEmail(string $email) {
        Validator::isEmail($email);
        $this->properties['email'] = $email;
        return $this;
    }

    public function setPhoneNumber(string $phoneNumber) {
        Validator::isPhoneNumber($phoneNumber);
        $this->properties['phoneNumber'] = $phoneNumber;
        return $this;
    }

    public function setEmailVerified(bool $emailVerified) {
        $this->properties['emailVerified'] = $emailVerified;
        return $this;
    }

    public function setDisplayName(string $displayName = null) {
        Validator::isNonEmptyString($displayName, 'Display name cannot be null or empty');
        $this->properties['displayName'] = $displayName;
        return $this;
    }

    public function setPhotoUrl(string $photoUrl) {
        Validator::isURL($photoUrl);
        $this->properties['photoUrl'] = $photoUrl;
        return $this;
    }

    public function setDisabled(bool $disabled) {
        $this->properties['disabled'] = $disabled;
        return $this;
    }

    public function setPassword(string $password) {
        Validator::isPassword($password);
        $this->properties['password'] = $password;
        return $this;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
