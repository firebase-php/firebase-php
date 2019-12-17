<?php

namespace Firebase\Util\Error;

/**
 * Messaging client error codes and their default messages.
 */
class MessagingClientErrorCode
{
    const INVALID_ARGUMENT = [
        'code' => 'invalid-argument',
        'message' => 'Invalid argument provided.',
    ];
    const INVALID_RECIPIENT = [
        'code' => 'invalid-recipient',
        'message' => 'Invalid message recipient provided.',
    ];
    const INVALID_PAYLOAD = [
        'code' => 'invalid-payload',
        'message' => 'Invalid message payload provided.',
    ];
    const INVALID_DATA_PAYLOAD_KEY = [
        'code' => 'invalid-data-payload-key',
        'message' => 'The data message payload contains an invalid key. See the reference documentation ' .
            'for the DataMessagePayload type for restricted keys.',
    ];
    const PAYLOAD_SIZE_LIMIT_EXCEEDED = [
        'code' => 'payload-size-limit-exceeded',
        'message' => 'The provided message payload exceeds the FCM size limits. See the error documentation ' .
            'for more details.',
    ];
    const INVALID_OPTIONS = [
        'code' => 'invalid-options',
        'message' => 'Invalid message options provided.',
    ];
    const INVALID_REGISTRATION_TOKEN = [
        'code' => 'invalid-registration-token',
        'message' => 'Invalid registration token provided. Make sure it matches the registration token ' .
            'the client app receives from registering with FCM.',
    ];
    const REGISTRATION_TOKEN_NOT_REGISTERED = [
        'code' => 'registration-token-not-registered',
        'message' => 'The provided registration token is not registered. A previously valid registration ' .
            'token can be unregistered for a variety of reasons. See the error documentation for more ' .
            'details. Remove this registration token and stop using it to send messages.',
    ];
    const MISMATCHED_CREDENTIAL = [
        'code' => 'mismatched-credential',
        'message' => 'The credential used to authenticate this SDK does not have permission to send ' .
            'messages to the device corresponding to the provided registration token. Make sure the ' .
            'credential and registration token both belong to the same Firebase project.',
    ];
    const INVALID_PACKAGE_NAME = [
        'code' => 'invalid-package-name',
        'message' => 'The message was addressed to a registration token whose package name does not match ' .
            'the provided "restrictedPackageName" option.',
    ];
    const DEVICE_MESSAGE_RATE_EXCEEDED = [
        'code' => 'device-message-rate-exceeded',
        'message' => 'The rate of messages to a particular device is too high. Reduce the number of ' .
            'messages sent to this device and do not immediately retry sending to this device.',
    ];
    const TOPICS_MESSAGE_RATE_EXCEEDED = [
        'code' => 'topics-message-rate-exceeded',
        'message' => 'The rate of messages to subscribers to a particular topic is too high. Reduce the ' .
            'number of messages sent for this topic, and do not immediately retry sending to this topic.',
    ];
    const MESSAGE_RATE_EXCEEDED = [
        'code' => 'message-rate-exceeded',
        'message' => 'Sending limit exceeded for the message target.',
    ];
    const THIRD_PARTY_AUTH_ERROR = [
        'code' => 'third-party-auth-error',
        'message' => 'A message targeted to an iOS device could not be sent because the required APNs ' .
            'SSL certificate was not uploaded or has expired. Check the validity of your development ' .
            'and production certificates.',
    ];
    const TOO_MANY_TOPICS = [
        'code' => 'too-many-topics',
        'message' => 'The maximum number of topics the provided registration token can be subscribed to ' .
            'has been exceeded.',
    ];
    const AUTHENTICATION_ERROR = [
        'code' => 'authentication-error',
        'message' => 'An error occurred when trying to authenticate to the FCM servers. Make sure the ' .
            'credential used to authenticate this SDK has the proper permissions. See ' .
            'https://firebase.google.com/docs/admin/setup for setup instructions.',
    ];
    const SERVER_UNAVAILABLE = [
        'code' => 'server-unavailable',
        'message' => 'The FCM server could not process the request in time. See the error documentation ' .
            'for more details.',
    ];
    const INTERNAL_ERROR = [
        'code' => 'internal-error',
        'message' => 'An internal error has occurred. Please retry the request.',
    ];
    const UNKNOWN_ERROR = [
        'code' => 'unknown-error',
        'message' => 'An unknown server error was returned.',
    ];
}
