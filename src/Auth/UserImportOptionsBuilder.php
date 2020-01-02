<?php


namespace Firebase\Auth;

use FirebaseHash\Hashable;

class UserImportOptionsBuilder
{
    /**
     * @var Hashable|null
     */
    private $hash;

    /**
     * @return Hashable|null
     */
    public function getHash(): ?Hashable
    {
        return $this->hash;
    }

    /**
     * @param Hashable|null $hash
     * @return UserImportOptionsBuilder
     */
    public function setHash(?Hashable $hash): UserImportOptionsBuilder
    {
        $this->hash = $hash;
        return $this;
    }

    public function build()
    {
        return new UserImportOptions($this);
    }
}
