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

    public function build() {
        return new UserImportOptions($this);
    }
}
