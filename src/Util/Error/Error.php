<?php

namespace Firebase\Util\Error;


class Error
{
    public static function authServerToClientCode(): array {
        return [
            'BILLING_NOT_ENABLED' => 'BILLING_NOT_ENABLED',
            'CLAIMS_TOO_LARGE' => 'CLAIMS_TOO_LARGE',
            'CONFIGURATION_EXISTS' => 'CONFIGURATION_EXISTS',
            'CONFIGURATION_NOT_FOUND' => 'CONFIGURATION_NOT_FOUND',
            'INSUFFICIENT_PERMISSION' => 'INSUFFICIENT_PERMISSION',
            'INVALID_CONFIG' => 'INVALID_CONFIG',
            'INVALID_CONFIG_ID' => 'INVALID_PROVIDER_ID',
            'INVALID_CONTINUE_URI' => 'INVALID_CONTINUE_URI',
            'INVALID_DYNAMIC_LINK_DOMAIN' => 'INVALID_DYNAMIC_LINK_DOMAIN',
            'DUPLICATE_EMAIL' => 'EMAIL_ALREADY_EXISTS',
            'DUPLICATE_LOCAL_ID' => 'UID_ALREADY_EXISTS',
            'EMAIL_EXISTS' => 'EMAIL_ALREADY_EXISTS',
            'FORBIDDEN_CLAIM' => 'FORBIDDEN_CLAIM',
            'INVALID_CLAIMS' => 'INVALID_CLAIMS',
            'INVALID_DURATION' => 'INVALID_SESSION_COOKIE_DURATION',
            'INVALID_EMAIL' => 'INVALID_EMAIL',
            'INVALID_DISPLAY_NAME' => 'INVALID_DISPLAY_NAME',
            'INVALID_ID_TOKEN' => 'INVALID_ID_TOKEN',
            'INVALID_NAME' => 'INVALID_NAME',
            'INVALID_OAUTH_CLIENT_ID' => 'INVALID_OAUTH_CLIENT_ID',
            'INVALID_PAGE_SELECTION' => 'INVALID_PAGE_TOKEN',
            'INVALID_PHONE_NUMBER' => 'INVALID_PHONE_NUMBER',
            'INVALID_PROJECT_ID' => 'INVALID_PROJECT_ID',
            'INVALID_PROVIDER_ID' => 'INVALID_PROVIDER_ID',
            'INVALID_SERVICE_ACCOUNT' => 'INVALID_SERVICE_ACCOUNT',
            'INVALID_TENANT_TYPE' => 'INVALID_TENANT_TYPE',
            'MISSING_ANDROID_PACKAGE_NAME' => 'MISSING_ANDROID_PACKAGE_NAME',
            'MISSING_CONFIG' => 'MISSING_CONFIG',
            'MISSING_CONFIG_ID' => 'MISSING_PROVIDER_ID',
            'MISSING_DISPLAY_NAME' => 'MISSING_DISPLAY_NAME',
            'MISSING_IOS_BUNDLE_ID' => 'MISSING_IOS_BUNDLE_ID',
            'MISSING_ISSUER' => 'MISSING_ISSUER',
            'MISSING_LOCAL_ID' => 'MISSING_UID',
            'MISSING_OAUTH_CLIENT_ID' => 'MISSING_OAUTH_CLIENT_ID',
            'MISSING_PROVIDER_ID' => 'MISSING_PROVIDER_ID',
            'MISSING_SAML_RELYING_PARTY_CONFIG' => 'MISSING_SAML_RELYING_PARTY_CONFIG',
            'MISSING_USER_ACCOUNT' => 'MISSING_UID',
            'OPERATION_NOT_ALLOWED' => 'OPERATION_NOT_ALLOWED',
            'PERMISSION_DENIED' => 'INSUFFICIENT_PERMISSION',
            'PHONE_NUMBER_EXISTS' => 'PHONE_NUMBER_ALREADY_EXISTS',
            'PROJECT_NOT_FOUND' => 'PROJECT_NOT_FOUND',
            'QUOTA_EXCEEDED' => 'QUOTA_EXCEEDED',
            'TENANT_NOT_FOUND' => 'TENANT_NOT_FOUND',
            'TENANT_ID_MISMATCH' => 'MISMATCHING_TENANT_ID',
            'TOKEN_EXPIRED' => 'ID_TOKEN_EXPIRED',
            'UNAUTHORIZED_DOMAIN' => 'UNAUTHORIZED_DOMAIN',
            'UNSUPPORTED_TENANT_OPERATION' => 'UNSUPPORTED_TENANT_OPERATION',
            'USER_NOT_FOUND' => 'USER_NOT_FOUND',
            'WEAK_PASSWORD' => 'INVALID_PASSWORD',
        ];
    }
}