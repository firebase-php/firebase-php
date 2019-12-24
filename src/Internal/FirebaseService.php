<?php


namespace Firebase\Internal;


use Firebase\Util\Validator\Validator;

abstract class FirebaseService
{
    private $id;

    protected $instance;

    protected function __construct(string $id, $instance)
    {
        $this->id = $id;
        $this->instance = Validator::isNonNullObject($instance);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
    }

    public abstract function destroy();
}
