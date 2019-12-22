<?php


namespace Firebase\Auth\FirebaseAuth;


class DecodedIdTokenFirebase
{
    /**
     * @var array
     */
    private $identities;

    /**
     * @var string
     */
    private $signInProvider;

    /**
     * @var array
     */
    private $extras;

    /**
     * @return array
     */
    public function getIdentities(): array
    {
        return $this->identities;
    }

    /**
     * @param array $identities
     * @return DecodedIdTokenFirebase
     */
    public function setIdentities(array $identities): DecodedIdTokenFirebase
    {
        $this->identities = $identities;
        return $this;
    }

    /**
     * @return string
     */
    public function getSignInProvider(): string
    {
        return $this->signInProvider;
    }

    /**
     * @param string $signInProvider
     * @return DecodedIdTokenFirebase
     */
    public function setSignInProvider(string $signInProvider): DecodedIdTokenFirebase
    {
        $this->signInProvider = $signInProvider;
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
     * @return DecodedIdTokenFirebase
     */
    public function setExtras(array $extras): DecodedIdTokenFirebase
    {
        $this->extras = $extras;
        return $this;
    }

    public function toArray() {
        return array_merge([
            'identities' => $this->identities,
            'sign_in_provider' => $this->signInProvider
        ], $this->extras);
    }
}
