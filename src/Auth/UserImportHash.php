<?php


namespace Firebase\Auth;

use Lcobucci\JWT\Signer;
use Respect\Validation\Validator as v;

abstract class UserImportHash implements Signer
{
    private $name;

    public function __construct(string $name)
    {
        v::stringType()->notEmpty()->assert($name);
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