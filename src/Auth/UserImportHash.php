<?php


namespace Firebase\Auth;

use Firebase\Util\Validator\Validator;
use Lcobucci\JWT\Signer;

abstract class UserImportHash implements Signer
{
    private $name;

    public function __construct(string $name)
    {
        Validator::isNonEmptyString($name);
        $this->name = $name;
    }

    public final function getProperties() {
        return array_merge(
            ['hashAlgorithm' => $this->name],
            $this->getOptions()
        );
    }

    /**
     * @return array
     */
    protected abstract function getOptions();
}