<?php


namespace Firebase\Auth;

use Lcobucci\JWT\Signer;

class UserImportOptionsBuilder
{
    /**
     * @var Signer
     */
    private $hash;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @return Signer
     */
    public function getHash(): ?Signer
    {
        return $this->hash;
    }

    /**
     * @param Signer $hash
     * @return UserImportOptionsBuilder
     */
    public function setHash(?Signer $hash = null): UserImportOptionsBuilder
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecretKey(): ?string
    {
        return $this->secretKey;
    }

    /**
     * @param string $secretKey
     * @return UserImportOptionsBuilder
     */
    public function setSecretKey(?string $secretKey): UserImportOptionsBuilder
    {
        $this->secretKey = $secretKey;
        return $this;
    }

    public function build()
    {
        return new UserImportOptions($this);
    }
}
