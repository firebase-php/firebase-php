<?php


namespace Firebase\Auth\UserImportOptions;


use Firebase\Auth\UserImportHash;
use Firebase\Auth\UserImportOptions;

class Builder
{
    /**
     * @var UserImportHash
     */
    private $hash;

    /**
     * @return UserImportHash
     */
    public function getHash(): UserImportHash
    {
        return $this->hash;
    }

    /**
     * @param UserImportHash $hash
     * @return Builder
     */
    public function setHash(UserImportHash $hash): Builder
    {
        $this->hash = $hash;
        return $this;
    }

    public function build() {
        return new UserImportOptions($this);
    }
}
