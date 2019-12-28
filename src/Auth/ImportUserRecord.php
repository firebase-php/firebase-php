<?php


namespace Firebase\Auth;

final class ImportUserRecord
{
    /**
     * @var array
     */
    private $properties;

    public function __construct(array $properties)
    {
        $this->properties = array_replace([], $properties);
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        $copy = array_replace([], $this->properties);

        // serialize custom claims
        if (isset($copy[UserRecord::CUSTOM_ATTRIBUTES])) {
            $customClaims = $copy[UserRecord::CUSTOM_ATTRIBUTES];
            $copy[UserRecord::CUSTOM_ATTRIBUTES] = UserRecord::serializeCustomClaims($customClaims);
        }

        return $copy;
    }

    public function hasPassword()
    {
        return isset($this->properties['passwordHash']);
    }

    public static function builder()
    {
        return new ImportUserRecordBuilder();
    }
}
