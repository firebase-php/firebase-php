<?php

namespace Firebase\Util\Validator;


class Validator
{
    public static function isArray($value): bool {
        return is_array($value);
    }

    public static function isNonEmptyArray($value): bool {
        return self::isArray($value) && count($value) !== 0;
    }

    public static function isBoolean($value): bool {
        return is_bool($value);
    }

    public static function isNumber($value): bool {
        return is_numeric($value) && !is_nan($value);
    }

    public static function isString($value): bool {
        return is_string($value);
    }

    public static function isBase64String($value): bool {
        if(!self::isString($value)) {
            return false;
        }

        preg_match('/^(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/', $value, $matches);
        return !empty($matches);
    }

    public static function isNonEmptyString($value): bool {
        return self::isString($value) && $value !== '';
    }

    public static function isObject($value): bool {
        return is_object($value) && !self::isArray($value);
    }

    public static function isNonNullObject($value): bool {
        return self::isObject($value) && !is_null($value);
    }

    public static function isUid($uid): bool {
        return self::isString($uid) && strlen($uid) > 0 && strlen($uid) <= 128;
    }

    public static function isPassword($password): bool {
        return self::isString($password) && strlen($password) >= 6;
    }

    public static function isEmail($email): bool {
        if(!self::isString($email)) {
            return false;
        }

        preg_match('/^[^@]+@[^@]+$/', $email, $matches);
        return !empty($matches);
    }

    public static function isPhoneNumber($phoneNumber): bool {
        if(!self::isString($phoneNumber)) {
            return false;
        }

        // Phone number validation is very lax here. Backend will enforce E.164
        // spec compliance and will normalize accordingly.
        // The phone number string must be non-empty and starts with a plus sign.
        $pattern1 = '/^\+/';

        // The phone number string must contain at least one alphanumeric character.
        $pattern2 = '/[\da-zA-Z]+/';

        preg_match($pattern1, $phoneNumber, $matches1);
        preg_match($pattern2, $phoneNumber, $matches2);

        return !empty($matches1) && !empty($matches2);
    }

    public static function isURL($urlStr): bool {
        return true;
    }

    public static function isTopic($topic): bool {
        if(!self::isString($topic)) {
            return false;
        }

        preg_match('/^(\/topics\/)?(private\/)?[a-zA-Z0-9-_.~%]+$/', $topic, $matches);
        return !empty($matches);
    }
}