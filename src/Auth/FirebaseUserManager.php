<?php


namespace Firebase\Auth;


use Firebase\Auth\Internal\GetAccountInfoResponse;
use Firebase\Auth\Internal\ResponseBuilder;
use Firebase\Auth\UserRecord\CreateRequest;
use Firebase\Auth\UserRecord\UpdateRequest;
use Firebase\FirebaseApp;
use Firebase\ImplFirebaseTrampolines;
use Firebase\Util\Validator\Validator;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class FirebaseUserManager
{
    const RESERVED_CLAIMS = ['amr', 'at_has', 'aud', 'auth_time', 'azp', 'cnf', 'c_hash', 'exp', 'iat', 'iss', 'jti', 'nbf', 'nonce', 'sub', 'firebase'];

    const USER_NOT_FOUND_ERROR = 'user-not-found';

    const INTERNAL_ERROR = 'internal-error';

    const ERROR_CODES = [
        'CLAIMS_TOO_LARGE' => 'claims-too-large',
        'CONFIGURATION_NOT_FOUND' => 'project-not-found',
        'INSUFFICIENT_PERMISSION' => 'insufficient-permission',
        'DUPLICATE_EMAIL' => 'email-already-exists',
        'DUPLICATE_LOCAL_ID' => 'uid-already-exists',
        'EMAIL_EXISTS' => 'email-already-exists',
        'INVALID_CLAIMS' => 'invalid-claims',
        'INVALID_EMAIL' => 'invalid-email',
        'INVALID_PAGE_SELECTION' => 'invalid-page-token',
        'INVALID_PHONE_NUMBER' => 'invalid-phone-number',
        'PHONE_NUMBER_EXISTS' => 'phone-number-already-exists',
        'PROJECT_NOT_FOUND' => 'project-not-found',
        'USER_NOT_FOUND' => self::USER_NOT_FOUND_ERROR,
        'WEAK_PASSWORD' => 'invalid-password',
        'UNAUTHORIZED_DOMAIN' => 'unauthorized-continue-uri',
        'INVALID_DYNAMIC_LINK_DOMAIN' => 'invalid-dynamic-link-domain',
    ];

    const MAX_LIST_USERS_RESULT = 1000;

    const MAX_IMPORT_USERS = 1000;

    private const ID_TOOLKIT_URL = 'https://identitytoolkit.googleapis.com/v1/projects/%s';

    private const CLIENT_VESION_HEADER = 'X-Client-Version';

    private $baseUrl;

    private $httpClient;

    public function __construct(FirebaseApp $app)
    {
        Validator::isNonNullObject($app, 'FirebaseApp must not be null');
        $projectId = ImplFirebaseTrampolines::getProjectId($app);
        Validator::isNonEmptyString($projectId, 'Project ID is required to access the auth service. Use a service account credential or set the project ID explicitly via FirebaseOptions. Alternatively you can also set the project ID via the GOOGLE_CLOUD_PROJECT environment variable.');
        $this->baseUrl = sprintf(self::ID_TOOLKIT_URL, $projectId);
        $this->httpClient = new Client(['baseUrl' => $this->baseUrl]);
    }

    public function getUserById(string $uid): UserRecord {
        $payload = ['localId' => [$uid]];
        /** @var GetAccountInfoResponse $response */
        $response = $this->post(
            '/accounts:lookup',
            $payload
        );
        if(empty($response->getUsers())) {
            throw new FirebaseAuthException(
                self::USER_NOT_FOUND_ERROR,
                "No user record found for the provided user ID: $uid"
            );
        }
        return new UserRecord($response->getUsers()[0]);
    }

    public function getUserByEmail(string $email): UserRecord {
        $payload = ['email' => [$email]];
        /** @var GetAccountInfoResponse $response */
        $response = $this->post(
            '/accounts:lookup',
            $payload
        );
        if(empty($response->getUsers())) {
            throw new FirebaseAuthException(
                self::USER_NOT_FOUND_ERROR,
                "No user record found for the provided email: $email"
            );
        }
        return new UserRecord($response->getUsers()[0]);
    }

    public function getUserByPhoneNumber(string $phoneNumber): UserRecord {
        $payload = ['phoneNumber' => [$phoneNumber]];
        /** @var GetAccountInfoResponse $response */
        $response = $this->post(
            '/accounts:lookup',
            $payload
        );
        if(empty($response->getUsers())) {
            throw new FirebaseAuthException(
                self::USER_NOT_FOUND_ERROR,
                "No user record found for the provided phone number: $phoneNumber"
            );
        }
        return new UserRecord($response->getUsers()[0]);
    }

    public function createUser(CreateRequest $request) {
        $response = $this->post(
            '/accounts',
            $request->getProperties()
        );
        if(empty($response)) {
            $uid = $response['localId'];
            if(is_string($uid) && !empty($uid)) {
                return $uid;
            }
        }
        throw new FirebaseAuthException(self::INTERNAL_ERROR, 'Failed to create new user');
    }

    public function updateUser(UpdateRequest $request) {
        $response = $this->post(
            '/accounts:update',
            $request->getProperties()
        );
        if(empty($response) || !$request->getUid() !== $response['localId']) {
            throw new FirebaseAuthException(self::INTERNAL_ERROR, 'Failed to update user: ' . $request->getUid());
        }
    }

    public function deleteUser(string $uid) {
        $payload = ['localId' => $uid];
        $response = $this->post(
            '/accounts:delete',
            $payload
        );
        if(empty($response) || !isset($response['kind'])) {
            throw new FirebaseAuthException(self::INTERNAL_ERROR, "Failed to delete user: $uid");
        }
    }

    public function listUsers(int $maxResults, string $pageToken) {
        $payload = ['maxResults' => $maxResults];
        if(is_string($pageToken)) {
            $payload['nextPageToken'] = $pageToken;
        }
        $path = '/accounts:batchGet';
        $response = $this->sendRequest('GET', $path, [], ['query' => $payload]);
        if(!isset($response)) {
            throw new FirebaseAuthException(self::INTERNAL_ERROR, 'Failed to retrieve users.');
        }

        return $response;
    }



    private function post(string $path, array $content) {
        Validator::isNonEmptyString($path, 'Path must not be null or empty');
        Validator::isNonNullObject($content, 'Content must not be null for POST requests');
        Validator::isNonEmptyArray($content, 'Content must not be null for POST requests');
        return $this->sendRequest('POST', $path, $content);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $content
     * @param array $requestOptions
     * @return array|null|bool
     * @throws FirebaseAuthException
     */
    private function sendRequest(
        string $method,
        string $path,
        array $content,
        array $requestOptions = []
    ) {
        Validator::isNonEmptyString($method, 'Method must not be null or empty');
        Validator::isNonEmptyString($path, 'URL path must not be null or empty');
        try {
            $request = new Request(
                $method,
                $path,
                [
                    self::CLIENT_VESION_HEADER => $this->clientVersion
                ],
                $content);
            $response = $this->httpClient->send($request, $requestOptions);
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new FirebaseAuthException(self::INTERNAL_ERROR, 'Error while calling user management backend service', $e);
        }
    }
}
