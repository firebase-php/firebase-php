<?php


namespace Firebase\Auth\TokenVerifier;


use Firebase\Util\Error\ErrorInfo;

class FirebaseTokenInfo
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $verifyApiName;

    /**
     * @var string
     */
    private $jwtName;

    /**
     * @var string
     */
    private $shortName;

    /**
     * @var ErrorInfo
     */
    private $expiredErrorCode;

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return FirebaseTokenInfo
     */
    public function setUrl(string $url): FirebaseTokenInfo
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getVerifyApiName(): string
    {
        return $this->verifyApiName;
    }

    /**
     * @param string $verifyApiName
     * @return FirebaseTokenInfo
     */
    public function setVerifyApiName(string $verifyApiName): FirebaseTokenInfo
    {
        $this->verifyApiName = $verifyApiName;
        return $this;
    }

    /**
     * @return string
     */
    public function getJwtName(): string
    {
        return $this->jwtName;
    }

    /**
     * @param string $jwtName
     * @return FirebaseTokenInfo
     */
    public function setJwtName(string $jwtName): FirebaseTokenInfo
    {
        $this->jwtName = $jwtName;
        return $this;
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @param string $shortName
     * @return FirebaseTokenInfo
     */
    public function setShortName(string $shortName): FirebaseTokenInfo
    {
        $this->shortName = $shortName;
        return $this;
    }

    /**
     * @return ErrorInfo
     */
    public function getExpiredErrorCode(): ErrorInfo
    {
        return $this->expiredErrorCode;
    }

    /**
     * @param ErrorInfo $expiredErrorCode
     * @return FirebaseTokenInfo
     */
    public function setExpiredErrorCode(ErrorInfo $expiredErrorCode): FirebaseTokenInfo
    {
        $this->expiredErrorCode = $expiredErrorCode;
        return $this;
    }


}
