<?php

namespace Firebase;

use Firebase\FirebaseApp\FirebaseAppOptions;
use Firebase\Util\Error\AppErrorCodes;
use Firebase\Util\Error\FirebaseAppError;
use Firebase\Util\Validator\Validator;

class FirebaseApp {
    private $name;

    private $options;

    private $services;

    private $deleted = false;

    public function __construct(FirebaseAppOptions $options, string $name)
    {
        $this->name = $name;
        $this->options = $options;

        if(!Validator::isNonNullObject($this->options)) {
            throw new FirebaseAppError(
                AppErrorCodes::INVALID_APP_OPTIONS,
                'Invalid Firebase app options passed as the first argument to initializeApp() for the app named ' . $this->name . '. Options must be a non-null object.'
            );
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return FirebaseApp
     */
    public function setName(string $name): FirebaseApp
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return FirebaseAppOptions
     */
    public function getOptions(): FirebaseAppOptions
    {
        return $this->options;
    }

    /**
     * @param FirebaseAppOptions $options
     * @return FirebaseApp
     */
    public function setOptions(FirebaseAppOptions $options): FirebaseApp
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param mixed $services
     * @return FirebaseApp
     */
    public function setServices($services)
    {
        $this->services = $services;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     * @return FirebaseApp
     */
    public function setDeleted(bool $deleted): FirebaseApp
    {
        $this->deleted = $deleted;
        return $this;
    }
}
