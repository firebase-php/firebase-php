<?php


namespace Firebase\Auth\UserRecord;


use Firebase\Util\Error\AuthClientErrorCode;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAuthError;

class UserInfo
{
    private $uid;

    private $displayName;

    private $email;

    private $photoURL;

    private $providerId;

    private $phoneNumber;

    public function __construct(array $response)
    {
        if(!isset($response['rawId']) || !isset($response['providerId'])) {
            throw new FirebaseAuthError(
                new ErrorInfo(AuthClientErrorCode::INTERNAL_ERROR),
                'INTERNAL ASSERT FAILED: Invalid user info response'
            );
        }

        $this->uid = $response['uid'];
        $this->displayName = $response['displayName'];
        $this->email = $response['email'];
        $this->photoURL = $response['photoURL'];
        $this->providerId = $response['providerId'];
        $this->phoneNumber = $response['phoneNumber'];
    }

    public function toArray() {
        return [
            'uid' => $this->uid,
            'displayName' => $this->displayName,
            'email' => $this->email,
            'photoURL' => $this->photoURL,
            'providerId' => $this->providerId,
            'phoneNumber' => $this->phoneNumber
        ];
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     * @return UserInfo
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param mixed $displayName
     * @return UserInfo
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return UserInfo
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhotoURL()
    {
        return $this->photoURL;
    }

    /**
     * @param mixed $photoURL
     * @return UserInfo
     */
    public function setPhotoURL($photoURL)
    {
        $this->photoURL = $photoURL;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProviderId()
    {
        return $this->providerId;
    }

    /**
     * @param mixed $providerId
     * @return UserInfo
     */
    public function setProviderId($providerId)
    {
        $this->providerId = $providerId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param mixed $phoneNumber
     * @return UserInfo
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }
}
