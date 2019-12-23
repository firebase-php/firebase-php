<?php


namespace Firebase\Auth\UserImportBuilder;


class UserImportRecord
{
    /**
     * @var array
     */
    private $properties;

    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     * @return UserImportRecord
     */
    public function setProperties(array $properties): UserImportRecord
    {
        $this->properties = $properties;
        return $this;
    }

    public function hasPassword() {
        return isset($this->properties['passwordHash']);
    }
}
