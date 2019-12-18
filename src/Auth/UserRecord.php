<?php


namespace Firebase\Auth;


use Firebase\Auth\Internal\GetAccountInfoResponse\User;
use Respect\Validation\Validator as v;
use Firebase\Util\Error\AuthClientErrorCode;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAuthError;

class UserRecord
{
    private $uid;

    private $email;

    private $phoneNumber;

    private $emailVerified;

    private $displayName;

    private $photoUrl;

    /**
     * @var bool
     */
    private $disabled;

    /**
     * @var ProviderUserInfo[]
     */
    private $providers;

    private $tokensValidAfterTimestamp;

    /**
     * @var UserMetadata
     */
    private $userMetadata;

    /**
     * @var array
     */
    private $customClaims;

    public function __construct(User $response)
    {
        v::objectType()->notEmpty()->assert($response);
        v::stringType()->notEmpty()->assert($response->getUid());
        $this->uid = $response->getUid();
        $this->email = $response->getEmail();
        $this->phoneNumber = $response->getPhoneNumber();
        $this->emailVerified = $response->isEmailVerified();
        $this->displayName = $response->getDisplayName();
        $this->photoUrl = $response->getPhotoUrl();
        $this->disabled = $response->isDisabled();
        $this->providers = [];

        if(v::arrayVal()->notEmpty()->validate($response->getProviders())) {
            foreach($response->getProviders() as $entry) {
                $this->providers[] = new ProviderUserInfo($entry);
            }
        }
        $this->tokensValidAfterTimestamp = $response->getValidSince() * 1000;
        $this->userMetadata = new UserMetadata($response->getCreatedAt(), $response->getLastLoginAt());
        $this->customClaims = $this->parseCustomClaims($response->getCustomClaims());
    }

    private function parseCustomClaims(string $customClaims) {
        return json_decode($customClaims, true);
    }
}
