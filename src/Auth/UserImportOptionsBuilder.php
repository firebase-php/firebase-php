<?php


namespace Firebase\Auth;


class UserImportOptionsBuilder
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
     * @return UserImportOptionsBuilder
     */
    public function setHash(UserImportHash $hash): UserImportOptionsBuilder
    {
        $this->hash = $hash;
        return $this;
    }

    public function build() {
        return new UserImportOptions($this);
    }
}
