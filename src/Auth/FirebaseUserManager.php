<?php


namespace Firebase\Auth;

use Firebase\Auth\Internal\DownloadAccountResponse;
use Firebase\Auth\Internal\EmailLinkType;
use Firebase\Auth\Internal\GetAccountInfoResponse;
use Firebase\Auth\Internal\UploadAccountResponse;
use Firebase\Auth\UserRecord\CreateRequest;
use Firebase\Auth\UserRecord\UpdateRequest;
use Firebase\FirebaseApp;
use Firebase\ImplFirebaseTrampolines;
use Firebase\Util\Validator\Validator;
use Google\Auth\CredentialsLoader;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use function GuzzleHttp\Psr7\build_query;

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

    private $baseUrl;

    private $app;

    public function __construct(FirebaseApp $app)
    {
        Validator::isNonNullObject($app, 'FirebaseApp must not be null');
        $this->app = $app;
        $projectId = ImplFirebaseTrampolines::getProjectId($app);
        Validator::isNonEmptyString($projectId, 'Project ID is required to access the auth service. Use a service account credential or set the project ID explicitly via FirebaseOptions. Alternatively you can also set the project ID via the GOOGLE_CLOUD_PROJECT environment variable.');
        $this->baseUrl = sprintf(self::ID_TOOLKIT_URL, $projectId);
    }

    public function getUserById(string $uid): UserRecord
    {
        $payload = ['localId' => [$uid]];
        $response = $this->post(
            '/accounts:lookup',
            $payload
        );
        $instance = GetAccountInfoResponse::build($response);
        if (empty($instance->getUsers())) {
            throw new FirebaseAuthException(
                self::USER_NOT_FOUND_ERROR,
                "No user record found for the provided user ID: $uid"
            );
        }
        return new UserRecord($instance->getUsers()[0]);
    }

    public function getUserByEmail(string $email): UserRecord
    {
        $payload = ['email' => [$email]];
        $response = $this->post(
            '/accounts:lookup',
            $payload
        );
        $instance = GetAccountInfoResponse::build($response);
        if (empty($instance->getUsers())) {
            throw new FirebaseAuthException(
                self::USER_NOT_FOUND_ERROR,
                "No user record found for the provided email: $email"
            );
        }
        return new UserRecord($instance->getUsers()[0]);
    }

    public function getUserByPhoneNumber(string $phoneNumber): UserRecord
    {
        $payload = ['phoneNumber' => [$phoneNumber]];
        $response = $this->post(
            '/accounts:lookup',
            $payload
        );
        $instance = GetAccountInfoResponse::build($response);
        if (empty($instance->getUsers())) {
            throw new FirebaseAuthException(
                self::USER_NOT_FOUND_ERROR,
                "No user record found for the provided phone number: $phoneNumber"
            );
        }
        return new UserRecord($instance->getUsers()[0]);
    }

    /**
     * @param CreateRequest $request
     * @return string
     * @throws FirebaseAuthException
     */
    public function createUser(CreateRequest $request)
    {
        $response = $this->post(
            '/accounts',
            $request->getProperties()
        );
        if (!empty($response)) {
            $uid = $response['localId'];
            if (is_string($uid) && !empty($uid)) {
                return $uid;
            }
        }
        throw new FirebaseAuthException(self::INTERNAL_ERROR, 'Failed to create new user');
    }

    /**
     * @param UpdateRequest $request
     * @throws FirebaseAuthException
     */
    public function updateUser(UpdateRequest $request)
    {
        $response = $this->post(
            '/accounts:update',
            $request->getProperties()
        );
        if (empty($response) || $request->getUid() !== $response['localId']) {
            throw new FirebaseAuthException(self::INTERNAL_ERROR, 'Failed to update user: ' . $request->getUid());
        }
    }

    /**
     * @param string $uid
     * @throws FirebaseAuthException
     */
    public function deleteUser(string $uid)
    {
        $payload = ['localId' => $uid];
        $response = $this->post(
            '/accounts:delete',
            $payload
        );
        if (empty($response) || !isset($response['kind'])) {
            throw new FirebaseAuthException(self::INTERNAL_ERROR, "Failed to delete user: $uid");
        }
    }

    public function listUsers(int $maxResults, ?string $pageToken = null)
    {
        $payload = ['maxResults' => $maxResults];
        if (is_string($pageToken)) {
            $payload['nextPageToken'] = $pageToken;
        }
        $path = '/accounts:batchGet';
        $response = $this->sendRequest('GET', $path, [], ['query' => $payload]);
        $instance = DownloadAccountResponse::build($response);
        if (empty($instance)) {
            throw new FirebaseAuthException(self::INTERNAL_ERROR, 'Failed to retrieve users');
        }

        return $instance;
    }

    public function importUsers(UserImportRequest $request = null): UserImportResult
    {
        Validator::isNonNullObject($request);
        $response = $this->post(
            '/accounts:batchCreate',
            $request->getPayload()
        );
        $instance = UploadAccountResponse::build($response);
        if (empty($instance)) {
            throw new FirebaseAuthException(self::INTERNAL_ERROR, 'Failed to import users');
        }

        return new UserImportResult($request->getUsersCount(), $instance);
    }

    public function createSessionCookie(
        string $idToken = null,
        SessionCookieOptions $options = null
    )
    {
        $payload = [
            'idToken' => $idToken,
            'validDuration' => $options->getExpiresInSeconds()
        ];
        $response = $this->post(':createSessionCookie', $payload);
        if (!empty($response)) {
            $cookie = $response['sessionCookie'];
            if (!empty($cookie)) {
                return $cookie;
            }
        }

        throw new FirebaseAuthException(self::INTERNAL_ERROR, 'Failed to create session cookie');
    }

    public function getEmailActionLink(
        EmailLinkType $type,
        string $email,
        ?ActionCodeSettings $settings
    )
    {
        $payload = [
            'requestType' => $type,
            'email' => $email,
            'returnOobLink' => true
        ];
        if (!is_null($settings)) {
            $payload = array_merge($payload, $settings->getProperties());
        }
        $response = $this->post('/accounts:sendOobCode', $payload);
        if (!empty($response)) {
            $link = $response['oobLink'];
            if (!empty($link)) {
                return $link;
            }
        }

        throw new FirebaseAuthException(self::INTERNAL_ERROR, 'Failed to create email action link');
    }

    private function post(string $path, array $content)
    {
        Validator::isNonEmptyString($path, 'Path must not be null or empty');
        Validator::isNonNullObject($content, 'Content must not be null for POST requests');
        return $this->sendRequest('POST', $path, $content);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $content
     * @param array $requestOptions
     * @return mixed|null
     * @throws FirebaseAuthException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function sendRequest(
        string $method,
        string $path,
        array $content,
        array $requestOptions = []
    )
    {
        Validator::isNonEmptyString($method, 'Method must not be null or empty');
        Validator::isNonEmptyString($path, 'URL path must not be null or empty');
        try {
            $body = null;
            $query = '';
            $uri = new Uri($this->baseUrl . $path);
            if ($method === 'GET') {
                $query = !isset($requestOptions['query']) ? '' : build_query($requestOptions['query']);
            } else {
                $body = empty($content) ? '{}' : json_encode($content);
            }
            $request = new Request(
                $method,
                $uri->withQuery($query),
                [],
                $body
            );
            $httpClient = $this->app->getOptions()->getHttpClient();
            $response = $httpClient->send($request, $requestOptions);
            if ($response->getStatusCode() >= 300) {
                throw new BadResponseException('Redirect exception', $request, $response);
            }
            $decoded = json_decode($response->getBody(), true);
            $error = json_last_error();
            if ($error !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException(json_last_error_msg(), json_last_error());
            }
            return $decoded;
        } catch (BadResponseException $e) {
            $this->handleHttpError($e);
            return null;
        } catch (\Exception $e) {
            throw new FirebaseAuthException(self::INTERNAL_ERROR, 'Error while calling user management backend service', $e);
        }
    }

    private function handleHttpError(BadResponseException $e)
    {
        $contents = $e->getResponse()->getBody()->getContents();
        try {
            $arr = json_decode($contents, true);
            $code = self::ERROR_CODES[$arr['error']['message'] ?? ''] ?? null;
            if (!is_null($code)) {
                throw new FirebaseAuthException($code, 'User management service responded with an error', $e);
            }
        } catch (\RuntimeException $e) {
        }

        $msg = sprintf(
            'Unexpected HTTP response with status: %d; body: %s',
            $e->getCode(),
            $contents
        );

        throw new FirebaseAuthException(self::INTERNAL_ERROR, $msg, $e);
    }
}
