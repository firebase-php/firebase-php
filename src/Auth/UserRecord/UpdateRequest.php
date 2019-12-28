<?php


namespace Firebase\Auth\UserRecord;


use Firebase\Auth\Internal\GetAccountInfoResponse\User;
use Firebase\Auth\UserRecord;
use Firebase\Util\Validator\Validator;

final class UpdateRequest extends UserRecordRequest
{
    public function __construct(string $uid)
    {
        Validator::isUid($uid);
        $this->properties['localId'] = $uid;
    }

    public function getUid() {
        return $this->properties['localId'];
    }

    public function setPhoneNumber(string $phoneNumber = null)
    {
        if(!is_null($phoneNumber)) {
            Validator::isPhoneNumber($phoneNumber);
        }

        $this->properties['phoneNumber'] = $phoneNumber;
        return $this;
    }

    public function setDisplayName(string $displayName = null)
    {
        $this->properties['displayName'] = $displayName;
        return $this;
    }

    public function setPhotoUrl(string $photoUrl = null)
    {
        if(!is_null($photoUrl)) {
            Validator::isUid($photoUrl);
        }
        $this->properties['photoUrl'] = $photoUrl;
        return $this;
    }

    public function setCustomClaims(array $customClaims = null) {
        UserRecord::checkCustomClaims($customClaims);
        $this->properties[UserRecord::CUSTOM_ATTRIBUTES] = $customClaims;
        return $this;
    }

    public function setValidSince(int $epochSeconds) {
        UserRecord::checkValidSince($epochSeconds);
        $this->properties['validSince'] = $epochSeconds;
        return $this;
    }

    public function getProperties(): array
    {
        $copy = array_replace([], $this->properties);
        $remove = [];

        foreach(UserRecord::REMOVABLE_FIELDS as $key => $entry) {
            if(in_array($key, array_keys($copy)) && is_null($copy[$key])) {
                $remove[] = $entry;
                unset($copy[$key]);
            }
        }

        if(!empty($remove)) {
            $copy['deleteAttribute'] = array_replace([], $remove);
        }

        if(in_array('phoneNumber', $copy) && is_null($copy['phoneNumber'])) {
            $copy['deleteProvider'] = ['phone'];
            unset($copy['phoneNumber']);
        }

        if(in_array(UserRecord::CUSTOM_ATTRIBUTES, $copy)) {
            $customClaims = array_replace([], $copy[UserRecord::CUSTOM_ATTRIBUTES]);
            $copy[UserRecord::CUSTOM_ATTRIBUTES] = UserRecord::serializeCustomClaims($customClaims);
        }

        return $copy;
    }
}
