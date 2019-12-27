<?php


namespace Firebase\Auth;

use Carbon\Carbon;
use Firebase\Auth\Internal\EmailLinkType;
use Firebase\Auth\Internal\FirebaseTokenFactory;
use Firebase\Auth\UserRecord\CreateRequest;
use Firebase\Auth\UserRecord\UpdateRequest;
use Firebase\FirebaseApp;
use Firebase\ImplFirebaseTrampolines;
use Firebase\Util\Validator\Validator;

class FirebaseAuth
{
    const SERVICE_ID = FirebaseAuth::class;

    const ERROR_CUSTOM_TOKEN = 'ERROR_CUSTOM_TOKEN';

    /**
     * @var bool
     */
    private $destroyed = false;

    /**
     * @var FirebaseApp
     */
    private $firebaseApp;

    /**
     * @var FirebaseTokenFactory
     */
    private $tokenFactory;

    /**
     * @var FirebaseTokenVerifier
     */
    private $idTokenVerifier;

    /**
     * @var FirebaseTokenVerifier
     */
    private $cookieVerifier;

    /**
     * @var FirebaseUserManager
     */
    private $userManager;

    public function __construct(FirebaseAuthBuilder $builder)
    {
        $this->firebaseApp = Validator::isNonNullObject($builder->getFirebaseApp());
        $this->tokenFactory = $builder->getTokenFactory();
        $this->idTokenVerifier = $builder->getIdTokenVerifier();
        $this->cookieVerifier = $builder->getCookieTokenVerifier();
        $this->userManager = new FirebaseUserManager($this->firebaseApp);
    }

    /**
     * @param FirebaseApp|null $app
     * @return FirebaseAuth
     * @throws \Exception
     */
    public static function getInstance(?FirebaseApp $app = null) {
        if(is_null($app)) {
            return self::getInstance(FirebaseApp::getInstance());
        } elseif($app->isDeleted()) {
            throw new \Exception();
        }

        $service = ImplFirebaseTrampolines::getService($app, self::SERVICE_ID, FirebaseAuthService::class);
        if(is_null($service)) {
            $service = ImplFirebaseTrampolines::addService($app, new FirebaseAuthService($app));
        }

        return $service->getInstance();
    }

    public static function fromApp(FirebaseApp $app) {
        return (new FirebaseAuthBuilder())
            ->setFirebaseApp($app)
            ->setTokenFactory(FirebaseTokenUtils::createTokenFactory($app))
            ->setIdTokenVerifier(FirebaseTokenUtils::createIdTokenVerifier($app))
            ->setCookieTokenVerifier(FirebaseTokenUtils::createSessionCookieVerifier($app))
            ->build();
    }

    public function createSessionCookie(string $idToken, SessionCookieOptions $options) {
        $this->checkNotDestroyed();
        Validator::isNonEmptyString($idToken, 'IdToken must not be null or empty');
        Validator::isNonNullObject($options, 'options must not be null');
        return $this->getUserManager()->createSessionCookie($idToken, $options);
    }

    public function verifySessionCookie(?string $cookie, ?bool $checkRevoked = false) {
        $this->checkNotDestroyed();
        Validator::isNonEmptyString($cookie, 'Session cookie must not be null or empty');
        return $this->getSessionCookieVerifier($checkRevoked)->verifyToken($cookie);
    }

    public function createCustomToken(string $uid, array $developerClaims = []) {
        $this->checkNotDestroyed();
        Validator::isNonEmptyString($uid, 'uid must not be null or empty');
        try {
            return $this->tokenFactory->createSignedCustomAuthTokenForUser($uid, $developerClaims);
        } catch (\Exception $e) {
            throw new FirebaseAuthException(self::ERROR_CUSTOM_TOKEN, 'Failed to generate a custom token', $e);
        }
    }

    public function verifyIdToken(string $token, bool $checkRevoked = false) {
        $this->checkNotDestroyed();
        Validator::isNonEmptyString($token, 'ID token must not be null or empty');
        return $this->getIdTokenVerifier()->verifyToken($token);
    }

    public function revokeRefreshToken(string $uid) {
        $this->checkNotDestroyed();
        Validator::isNonEmptyString($uid, 'uid must not be null or empty');
        $currentTimeSeconds = intval(Carbon::now()->timestamp/1000);
        $request = (new UpdateRequest($uid))->setValidSince($currentTimeSeconds);
        $this->getUserManager()->updateUser($request);
    }

    public function getUser(string $uid) {
        $this->checkNotDestroyed();
        Validator::isNonEmptyString($uid, 'uid must not be null or empty');
        return $this->getUserManager()->getUserById($uid);
    }

    public function getUserByEmail(string $email) {
        $this->checkNotDestroyed();
        Validator::isNonEmptyString($email, 'email must not be null or empty');
        return $this->getUserManager()->getUserByEmail($email);
    }

    public function getUserByPhoneNumber(string $phoneNumber) {
        $this->checkNotDestroyed();
        Validator::isNonEmptyString($phoneNumber, 'phone number must not be null or empty');
        return $this->getUserManager()->getUserByPhoneNumber($phoneNumber);
    }

    public function listUsers(string $pageToken, int $maxResults = FirebaseUserManager::MAX_LIST_USERS_RESULT) {
        $this->checkNotDestroyed();
        return $this->getUserManager()->listUsers($maxResults, $pageToken);
    }

    public function createUser(CreateRequest $request = null) {
        $this->checkNotDestroyed();
        Validator::isNonNullObject($request, 'create request must not be null');
        $userManager = $this->getUserManager();
        $uid = $userManager->createUser($request);
        return $userManager->getUserById($uid);
    }

    public function updateUser(UpdateRequest $request = null) {
        $this->checkNotDestroyed();
        Validator::isNonNullObject($request, 'update request must not be null');
        $userManager = $this->getUserManager();
        $userManager->updateUser($request);
        return $userManager->getUserById($request->getUid());
    }

    public function setCustomUserClaims(string $uid, array $claims = null) {
        $this->checkNotDestroyed();
        Validator::isNonEmptyString($uid, 'uid must not be null or empty');
        $userManager = $this->getUserManager();
        $request = (new UpdateRequest($uid))->setCustomClaims($claims);
        $userManager->updateUser($request);
        return null;
    }

    public function deleteUser(string $uid) {
        $this->checkNotDestroyed();
        Validator::isNonEmptyString($uid, 'uid must not be null or empty');
        $this->getUserManager()->deleteUser($uid);
        return null;
    }

    /**
     * @param ImportUserRecord[]|null $users
     * @param UserImportOptions|null $options
     * @return UserImportResult
     * @throws FirebaseAuthException
     */
    public function importUsers(array $users = null, UserImportOptions $options = null) {
        $this->checkNotDestroyed();
        $request = new UserImportRequest($users, $options);
        return $this->getUserManager()->importUsers($request);
    }

    public function generatePasswordResetLink(string $email, ActionCodeSettings $settings = null) {
        return $this->generateEmailActionLink(EmailLinkType::PASSWORD_RESET(), $email, $settings);
    }

    public function generateEmailVerificationLink(string $email, ActionCodeSettings $settings = null) {
        return $this->generateEmailActionLink(EmailLinkType::VERIFY_EMAIL(), $email, $settings);
    }

    public function generateSignInWithEmailLink(string $email, ActionCodeSettings $settings = null) {
        return $this->generateEmailActionLink(EmailLinkType::EMAIL_SIGNIN(), $email, $settings);
    }

    private function generateEmailActionLink(EmailLinkType $type, string $email, ActionCodeSettings $settings = null) {
        $this->checkNotDestroyed();
        Validator::isNonEmptyString($email, 'email must not be null or empty');
        if($type === EmailLinkType::EMAIL_SIGNIN()) {
            Validator::isNonNullObject($settings, 'ActionCodeSettings must not be null when generating sign-in links');
        }

        return $this->getUserManager()->getEmailActionLink($type, $email, $settings);
    }

    public function getUserManager() {
        return $this->userManager;
    }

    public function getSessionCookieVerifier(bool $checkRevoked = false) {
        $verifier = $this->cookieVerifier;
        if($checkRevoked) {
            $userManager = $this->getUserManager();
            $verifier = RevocationCheckDecorator::decorateSessionCookieVerifier($verifier, $userManager);
        }
        return $verifier;
    }

    public function getIdTokenVerifier(bool $checkRevoked = false) {
        $verifier = $this->idTokenVerifier;
        if($checkRevoked) {
            $userManager = $this->getUserManager();
            $verifier = RevocationCheckDecorator::decorateIdTokenVerifier($verifier, $userManager);
        }

        return $verifier;
    }

    private function checkNotDestroyed() {
        Validator::checkArgument(!$this->destroyed, 'FirebaseAuth instance is no longer alive. This happens when the parent FirebaseApp instance has been deleted.');
    }

    public function destroy() {
        $this->destroyed = true;
    }

    static function builder() {
        return new FirebaseAuthBuilder();
    }
}
