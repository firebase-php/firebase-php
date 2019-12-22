<?php


namespace Firebase\Auth\FirebaseAuth;


use Carbon\Carbon;

class DecodedIdToken
{
    /**
     * @var string
     */
    private $aud;

    /**
     * @var Carbon
     */
    private $authTime;

    /**
     * @var Carbon
     */
    private $exp;

    /**
     * @var DecodedIdTokenFirebase
     */
    private $firebase;

    /**
     * @var Carbon
     */
    private $iat;

    /**
     * @var string
     */
    private $iss;

    /**
     * @var string
     */
    private $sub;

    /**
     * @var string
     */
    private $tenant;

    /**
     * @var array
     */
    private $extras;

    /**
     * @return string
     */
    public function getAud(): string
    {
        return $this->aud;
    }

    /**
     * @param string $aud
     * @return DecodedIdToken
     */
    public function setAud(string $aud): DecodedIdToken
    {
        $this->aud = $aud;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getAuthTime(): Carbon
    {
        return $this->authTime;
    }

    /**
     * @param Carbon $authTime
     * @return DecodedIdToken
     */
    public function setAuthTime(Carbon $authTime): DecodedIdToken
    {
        $this->authTime = $authTime;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getExp(): Carbon
    {
        return $this->exp;
    }

    /**
     * @param Carbon $exp
     * @return DecodedIdToken
     */
    public function setExp(Carbon $exp): DecodedIdToken
    {
        $this->exp = $exp;
        return $this;
    }

    /**
     * @return DecodedIdTokenFirebase
     */
    public function getFirebase(): DecodedIdTokenFirebase
    {
        return $this->firebase;
    }

    /**
     * @param DecodedIdTokenFirebase $firebase
     * @return DecodedIdToken
     */
    public function setFirebase(DecodedIdTokenFirebase $firebase): DecodedIdToken
    {
        $this->firebase = $firebase;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getIat(): Carbon
    {
        return $this->iat;
    }

    /**
     * @param Carbon $iat
     * @return DecodedIdToken
     */
    public function setIat(Carbon $iat): DecodedIdToken
    {
        $this->iat = $iat;
        return $this;
    }

    /**
     * @return string
     */
    public function getIss(): string
    {
        return $this->iss;
    }

    /**
     * @param string $iss
     * @return DecodedIdToken
     */
    public function setIss(string $iss): DecodedIdToken
    {
        $this->iss = $iss;
        return $this;
    }

    /**
     * @return string
     */
    public function getSub(): string
    {
        return $this->sub;
    }

    /**
     * @param string $sub
     * @return DecodedIdToken
     */
    public function setSub(string $sub): DecodedIdToken
    {
        $this->sub = $sub;
        return $this;
    }

    /**
     * @return string
     */
    public function getTenant(): string
    {
        return $this->tenant;
    }

    /**
     * @param string $tenant
     * @return DecodedIdToken
     */
    public function setTenant(string $tenant): DecodedIdToken
    {
        $this->tenant = $tenant;
        return $this;
    }

    /**
     * @return array
     */
    public function getExtras(): array
    {
        return $this->extras;
    }

    /**
     * @param array $extras
     * @return DecodedIdToken
     */
    public function setExtras(array $extras): DecodedIdToken
    {
        $this->extras = $extras;
        return $this;
    }

    public function toArray() {
        return array_merge([
            'aud' => $this->aud,
            'auth_time' => $this->authTime->getTimestamp(),
            'exp' => $this->exp->getTimestamp(),
            'firebase' => $this->firebase->toArray(),
            'iat' => $this->iat->getTimestamp(),
            'iss' => $this->iss,
            'sub' => $this->sub,
            'tenant' => $this->tenant
        ], $this->extras);
    }
}
