<?php


namespace Firebase\Util\Error;

class AuthClientErrorCode
{
    const BILLING_NOT_ENABLED = [
        'code' => 'billing-not-enabled',
        'message' => 'Feature requires billing to be enabled.',
    ];
    const CLAIMS_TOO_LARGE = [
        'code' => 'claims-too-large',
        'message' => 'Developer claims maximum payload size exceeded.',
    ];
    const CONFIGURATION_EXISTS = [
        'code' => 'configuration-exists',
        'message' => 'A configuration already exists with the provided identifier.',
    ];
    const CONFIGURATION_NOT_FOUND = [
        'code' => 'configuration-not-found',
        'message' => 'There is no configuration corresponding to the provided identifier.',
    ];
    const ID_TOKEN_EXPIRED = [
        'code' => 'id-token-expired',
        'message' => 'The provided Firebase ID token is expired.',
    ];
    const INVALID_ARGUMENT = [
        'code' => 'argument-error',
        'message' => 'Invalid argument provided.',
    ];
    const INVALID_CONFIG = [
        'code' => 'invalid-config',
        'message' => 'The provided configuration is invalid.',
    ];
    const EMAIL_ALREADY_EXISTS = [
        'code' => 'email-already-exists',
        'message' => 'The email address is already in use by another account.',
    ];
    const FORBIDDEN_CLAIM = [
        'code' => 'reserved-claim',
        'message' => 'The specified developer claim is reserved and cannot be specified.',
    ];
    const INVALID_ID_TOKEN = [
        'code' => 'invalid-id-token',
        'message' => 'The provided ID token is not a valid Firebase ID token.',
    ];
    const ID_TOKEN_REVOKED = [
        'code' => 'id-token-revoked',
        'message' => 'The Firebase ID token has been revoked.',
    ];
    const INTERNAL_ERROR = [
        'code' => 'internal-error',
        'message' => 'An internal error has occurred.',
    ];
    const INVALID_CLAIMS = [
        'code' => 'invalid-claims',
        'message' => 'The provided custom claim attributes are invalid.',
    ];
    const INVALID_CONTINUE_URI = [
        'code' => 'invalid-continue-uri',
        'message' => 'The continue URL must be a valid URL string.',
    ];
    const INVALID_CREATION_TIME = [
        'code' => 'invalid-creation-time',
        'message' => 'The creation time must be a valid UTC date string.',
    ];
    const INVALID_CREDENTIAL = [
        'code' => 'invalid-credential',
        'message' => 'Invalid credential object provided.',
    ];
    const INVALID_DISABLED_FIELD = [
        'code' => 'invalid-disabled-field',
        'message' => 'The disabled field must be a boolean.',
    ];
    const INVALID_DISPLAY_NAME = [
        'code' => 'invalid-display-name',
        'message' => 'The displayName field must be a valid string.',
    ];
    const INVALID_DYNAMIC_LINK_DOMAIN = [
        'code' => 'invalid-dynamic-link-domain',
        'message' => 'The provided dynamic link domain is not configured or authorized ' .
            'for the current project.',
    ];
    const INVALID_EMAIL_VERIFIED = [
        'code' => 'invalid-email-verified',
        'message' => 'The emailVerified field must be a boolean.',
    ];
    const INVALID_EMAIL = [
        'code' => 'invalid-email',
        'message' => 'The email address is improperly formatted.',
    ];
    const INVALID_HASH_ALGORITHM = [
        'code' => 'invalid-hash-algorithm',
        'message' => 'The hash algorithm must match one of the strings in the list of ' .
            'supported algorithms.',
    ];
    const INVALID_HASH_BLOCK_SIZE = [
        'code' => 'invalid-hash-block-size',
        'message' => 'The hash block size must be a valid number.',
    ];
    const INVALID_HASH_DERIVED_KEY_LENGTH = [
        'code' => 'invalid-hash-derived-key-length',
        'message' => 'The hash derived key length must be a valid number.',
    ];
    const INVALID_HASH_KEY = [
        'code' => 'invalid-hash-key',
        'message' => 'The hash key must a valid byte buffer.',
    ];
    const INVALID_HASH_MEMORY_COST = [
        'code' => 'invalid-hash-memory-cost',
        'message' => 'The hash memory cost must be a valid number.',
    ];
    const INVALID_HASH_PARALLELIZATION = [
        'code' => 'invalid-hash-parallelization',
        'message' => 'The hash parallelization must be a valid number.',
    ];
    const INVALID_HASH_ROUNDS = [
        'code' => 'invalid-hash-rounds',
        'message' => 'The hash rounds must be a valid number.',
    ];
    const INVALID_HASH_SALT_SEPARATOR = [
        'code' => 'invalid-hash-salt-separator',
        'message' => 'The hashing algorithm salt separator field must be a valid byte buffer.',
    ];
    const INVALID_LAST_SIGN_IN_TIME = [
        'code' => 'invalid-last-sign-in-time',
        'message' => 'The last sign-in time must be a valid UTC date string.',
    ];
    const INVALID_NAME = [
        'code' => 'invalid-name',
        'message' => 'The resource name provided is invalid.',
    ];
    const INVALID_OAUTH_CLIENT_ID = [
        'code' => 'invalid-oauth-client-id',
        'message' => 'The provided OAuth client ID is invalid.',
    ];
    const INVALID_PAGE_TOKEN = [
        'code' => 'invalid-page-token',
        'message' => 'The page token must be a valid non-empty string.',
    ];
    const INVALID_PASSWORD = [
        'code' => 'invalid-password',
        'message' => 'The password must be a string with at least 6 characters.',
    ];
    const INVALID_PASSWORD_HASH = [
        'code' => 'invalid-password-hash',
        'message' => 'The password hash must be a valid byte buffer.',
    ];
    const INVALID_PASSWORD_SALT = [
        'code' => 'invalid-password-salt',
        'message' => 'The password salt must be a valid byte buffer.',
    ];
    const INVALID_PHONE_NUMBER = [
        'code' => 'invalid-phone-number',
        'message' => 'The phone number must be a non-empty E.164 standard compliant identifier ' .
            'string.',
    ];
    const INVALID_PHOTO_URL = [
        'code' => 'invalid-photo-url',
        'message' => 'The photoURL field must be a valid URL.',
    ];
    const INVALID_PROJECT_ID = [
        'code' => 'invalid-project-id',
        'message' => 'Invalid parent project. Either parent project doesn\'t exist or didn\'t enable multi-tenancy.',
    ];
    const INVALID_PROVIDER_DATA = [
        'code' => 'invalid-provider-data',
        'message' => 'The providerData must be a valid array of UserInfo objects.',
    ];
    const INVALID_PROVIDER_ID = [
        'code' => 'invalid-provider-id',
        'message' => 'The providerId must be a valid supported provider identifier string.',
    ];
    const INVALID_SESSION_COOKIE_DURATION = [
        'code' => 'invalid-session-cookie-duration',
        'message' => 'The session cookie duration must be a valid number in milliseconds ' .
            'between 5 minutes and 2 weeks.',
    ];
    const INVALID_TENANT_ID = [
        'code' => 'invalid-tenant-id',
        'message' => 'The tenant ID must be a valid non-empty string.',
    ];
    const INVALID_TENANT_TYPE = [
        'code' => 'invalid-tenant-type',
        'message' => 'Tenant type must be either "full_service" or "lightweight".',
    ];
    const INVALID_UID = [
        'code' => 'invalid-uid',
        'message' => 'The uid must be a non-empty string with at most 128 characters.',
    ];
    const INVALID_USER_IMPORT = [
        'code' => 'invalid-user-import',
        'message' => 'The user record to import is invalid.',
    ];
    const INVALID_TOKENS_VALID_AFTER_TIME = [
        'code' => 'invalid-tokens-valid-after-time',
        'message' => 'The tokensValidAfterTime must be a valid UTC number in seconds.',
    ];
    const MISMATCHING_TENANT_ID = [
        'code' => 'mismatching-tenant-id',
        'message' => 'User tenant ID does not match with the current TenantAwareAuth tenant ID.',
    ];
    const MISSING_ANDROID_PACKAGE_NAME = [
        'code' => 'missing-android-pkg-name',
        'message' => 'An Android Package Name must be provided if the Android App is ' .
            'required to be installed.',
    ];
    const MISSING_CONFIG = [
        'code' => 'missing-config',
        'message' => 'The provided configuration is missing required attributes.',
    ];
    const MISSING_CONTINUE_URI = [
        'code' => 'missing-continue-uri',
        'message' => 'A valid continue URL must be provided in the request.',
    ];
    const MISSING_DISPLAY_NAME = [
        'code' => 'missing-display-name',
        'message' => 'The resource being created or edited is missing a valid display name.',
    ];
    const MISSING_IOS_BUNDLE_ID = [
        'code' => 'missing-ios-bundle-id',
        'message' => 'The request is missing an iOS Bundle ID.',
    ];
    const MISSING_ISSUER = [
        'code' => 'missing-issuer',
        'message' => 'The OAuth/OIDC configuration issuer must not be empty.',
    ];
    const MISSING_HASH_ALGORITHM = [
        'code' => 'missing-hash-algorithm',
        'message' => 'Importing users with password hashes requires that the hashing ' .
            'algorithm and its parameters be provided.',
    ];
    const MISSING_OAUTH_CLIENT_ID = [
        'code' => 'missing-oauth-client-id',
        'message' => 'The OAuth/OIDC configuration client ID must not be empty.',
    ];
    const MISSING_PROVIDER_ID = [
        'code' => 'missing-provider-id',
        'message' => 'A valid provider ID must be provided in the request.',
    ];
    const MISSING_SAML_RELYING_PARTY_CONFIG = [
        'code' => 'missing-saml-relying-party-config',
        'message' => 'The SAML configuration provided is missing a relying party configuration.',
    ];
    const MAXIMUM_USER_COUNT_EXCEEDED = [
        'code' => 'maximum-user-count-exceeded',
        'message' => 'The maximum allowed number of users to import has been exceeded.',
    ];
    const MISSING_UID = [
        'code' => 'missing-uid',
        'message' => 'A uid identifier is required for the current operation.',
    ];
    const OPERATION_NOT_ALLOWED = [
        'code' => 'operation-not-allowed',
        'message' => 'The given sign-in provider is disabled for this Firebase project. ' .
            'Enable it in the Firebase console, under the sign-in method tab of the ' .
            'Auth section.',
    ];
    const PHONE_NUMBER_ALREADY_EXISTS = [
        'code' => 'phone-number-already-exists',
        'message' => 'The user with the provided phone number already exists.',
    ];
    const PROJECT_NOT_FOUND = [
        'code' => 'project-not-found',
        'message' => 'No Firebase project was found for the provided credential.',
    ];
    const INSUFFICIENT_PERMISSION = [
        'code' => 'insufficient-permission',
        'message' => 'Credential implementation provided to initializeApp() via the "credential" property ' .
            'has insufficient permission to access the requested resource. See ' .
            'https://firebase.google.com/docs/admin/setup for details on how to authenticate this SDK ' .
            'with appropriate permissions.',
    ];
    const QUOTA_EXCEEDED = [
        'code' => 'quota-exceeded',
        'message' => 'The project quota for the specified operation has been exceeded.',
    ];
    const SESSION_COOKIE_EXPIRED = [
        'code' => 'session-cookie-expired',
        'message' => 'The Firebase session cookie is expired.',
    ];
    const SESSION_COOKIE_REVOKED = [
        'code' => 'session-cookie-revoked',
        'message' => 'The Firebase session cookie has been revoked.',
    ];
    const TENANT_NOT_FOUND = [
        'code' => 'tenant-not-found',
        'message' => 'There is no tenant corresponding to the provided identifier.',
    ];
    const UID_ALREADY_EXISTS = [
        'code' => 'uid-already-exists',
        'message' => 'The user with the provided uid already exists.',
    ];
    const UNAUTHORIZED_DOMAIN = [
        'code' => 'unauthorized-continue-uri',
        'message' => 'The domain of the continue URL is not whitelisted. Whitelist the domain in the ' .
            'Firebase console.',
    ];
    const UNSUPPORTED_TENANT_OPERATION = [
        'code' => 'unsupported-tenant-operation',
        'message' => 'This operation is not supported in a multi-tenant context.',
    ];
    const USER_NOT_FOUND = [
        'code' => 'user-not-found',
        'message' => 'There is no user record corresponding to the provided identifier.',
    ];
    const NOT_FOUND = [
        'code' => 'not-found',
        'message' => 'The requested resource was not found.',
    ];
}
