<?php


namespace Firebase\Auth;

use Firebase\Auth\Internal\DownloadAccountResponse\User;

class ExportedUserRecord extends UserRecord
{
    /**
     * @var string|null
     */
    private $passwordHash;

    /**
     * @var string|null
     */
    private $passwordSalt;

    private static function REDACTED_BASE64()
    {
        return base64_encode('REDACTED');
    }

    public function __construct(User $response = null)
    {
        parent::__construct($response);
        $passwordHash = $response->getPasswordHash();

        if (!is_null($passwordHash) && $passwordHash != self::REDACTED_BASE64()) {
            $this->passwordHash = $passwordHash;
        } else {
            $this->passwordHash = null;
        }
        $this->passwordSalt = $response->getPasswordSalt();
    }

    /**
     * Returns the user's password hash as a base64-encoded string.
     *
     * <p>If the Firebase Auth hashing algorithm (SCRYPT) was used to create the user account,
     * returns the base64-encoded password hash of the user. If a different hashing algorithm was
     * used to create this user, as is typical when migrating from another Auth system, returns
     * an empty string. Returns null if no password is set.
     *
     * @return string|null A base64-encoded password hash, possibly empty or null.
     */
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * Returns the user's password salt as a base64-encoded string.
     *
     * <p>If the Firebase Auth hashing algorithm (SCRYPT) was used to create the user account,
     * returns the base64-encoded password salt of the user. If a different hashing algorithm was
     * used to create this user, as is typical when migrating from another Auth system, returns
     * an empty string. Returns null if no password is set.
     *
     * @return string|null A base64-encoded password salt, possibly empty or null.
     */
    public function getPasswordSalt(): ?string
    {
        return $this->passwordSalt;
    }
}
