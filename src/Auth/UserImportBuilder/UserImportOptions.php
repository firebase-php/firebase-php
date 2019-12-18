<?php


namespace Firebase\Auth\UserImportBuilder;


class UserImportOptions
{
    private $hash;

    public function __construct(UserImportOptionsHash $hash)
    {
        $this->hash = $hash;
    }

    public function toArray() {
        return [
            'hash' => $this->hash->toArray()
        ];
    }
}
