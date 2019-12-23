<?php


namespace Firebase\Auth;


use Firebase\Auth\UserImportBuilder\UserImportRecord;
use Firebase\Util\Validator\Validator;

class UserImportRequest
{
    private $users;

    private $payload;

    /**
     * UserImportRequest constructor.
     * @param UserImportRecord[]|null $users
     * @param UserImportOptions|null $options
     */
    public function __construct(array $users = null, UserImportOptions $options = null)
    {
        Validator::checkArgument(is_array($users) && !empty($users), 'Users must not be null or empty');
        Validator::checkArgument(
            count($users) <= FirebaseUserManager::MAX_IMPORT_USERS,
            sprintf('Users list must not contain more than %s items', FirebaseUserManager::MAX_IMPORT_USERS)
        );
        $hasPassword = false;
        $this->users = [];
        foreach($users as $user) {
            if($user->hasPassword()) {
                $hasPassword = true;
            }
            $this->users[] = $user->getProperties();
        }
        if($hasPassword) {
            Validator::checkArgument(
                !is_null($options) && !is_null($options->getHash()),
                'UserImportHash option is required when at least one user has a password. Provide '
                . 'a UserImportHash via UserImportOptions.withHash().'
            );
        }
        $this->payload = array_merge(['users' => $this->users], $options);
    }

    public function getUsersCount() {
        return count($this->users);
    }

    public function getPayload() {
        return $this->payload;
    }
}
