<?php

namespace Firebase\Util\Validator;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

class Validator
{
    public static function isArray($value): bool
    {
        return is_array($value);
    }
    public static function isBoolean($value): bool
    {
        return is_bool($value);
    }

    public static function isNumber($value): bool
    {
        return is_numeric($value) && !is_nan($value);
    }

    public static function isString($value): bool
    {
        return is_string($value);
    }

    public static function isBase64String($value): bool
    {
        if (!self::isString($value)) {
            return false;
        }

        preg_match('/^(?:[A-Za-z0-9+\\/]{4})*(?:[A-Za-z0-9+\\/]{2}==|[A-Za-z0-9+\\/]{3}=)?$/', $value, $matches);
        return !empty($matches);
    }

    public static function isNonEmptyString($value, $message = null, $throwable = true)
    {
        return self::isNonEmptyArray($value, $message, $throwable);
    }

    public static function isNonEmptyArray($value, $message = null, $throwable = true)
    {
        $rule = new NotBlank(is_null($message) ? [] : ['message' => $message]);
        $violations = self::validator()->validate($value, [$rule]);
        try {
            self::check($violations, $rule->message);
        } catch (\Exception $e) {
            if ($throwable) {
                throw $e;
            } else {
                return false;
            }
        }
        return $value;
    }

    public static function isNonNullObject($value, $message = null)
    {
        $rule = new NotNull(is_null($message) ? [] : ['message' => $message]);
        $violations = self::validator()->validate($value, [$rule]);
        self::check($violations, $rule->message);
        return $value;
    }

    public static function isUid($uid)
    {
        self::isNonEmptyString($uid, 'UID cannot be null or empty');
        $rule = new Length([
            'max' => 128,
            'maxMessage' => 'UID cannot be longer than 128 characters'
        ]);
        $violations = self::validator()->validate($uid, [$rule]);
        self::check($violations, $rule->maxMessage);
        return $uid;
    }

    public static function isPassword($password)
    {
        self::isNonEmptyString($password);
        $rule = new Length([
            'min' => 6,
            'minMessage' => 'Password must be at least 6 characters long'
        ]);
        $violations = self::validator()->validate($password, [$rule]);
        self::check($violations, $rule->minMessage);
    }

    public static function isEmail($email)
    {
        self::isNonEmptyString($email, 'Email cannot be null or empty');
        $rule = new Email();
        $violations = self::validator()->validate($email, [$rule]);
        self::check($violations, $rule->message);
        return $email;
    }

    public static function isPhoneNumber($phoneNumber)
    {
        self::isNonEmptyString($phoneNumber, 'Phone number cannot be null or empty');
        $rule = new Regex(['pattern' => '/^\\+[\\da-zA-Z]+/', 'message' => "Phone number must be a valid, E.164 compliant identifier starting with a '+' sign"]);
        $violations = self::validator()->validate($phoneNumber, [$rule]);
        self::check($violations, $rule->message);
        return $phoneNumber;
    }

    public static function isURL($url, $subject = 'URL')
    {
        self::isNonEmptyString($url, $subject . ' cannot be null or empty');
        $rule = new Url(['message' => $subject . ' is an invalid URL']);
        $violations = self::validator()->validate($url, [$rule]);
        self::check($violations, $rule->message);
        return $url;
    }

    public static function isTopic($topic): bool
    {
        if (!self::isString($topic)) {
            return false;
        }

        preg_match('/^(\\/topics\\/)?(private\\/)?[a-zA-Z0-9-_.~%]+$/', $topic, $matches);
        return !empty($matches);
    }

    public static function checkArgument(bool $value, $message = null)
    {
        $rule = new IsTrue();
        $violations = self::validator()->validate($value, [$rule]);
        self::check($violations, $message || $rule->message);
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private static function validator()
    {
        return Validation::createValidator();
    }

    /**
     * @param ConstraintViolationListInterface $violations
     * @param string|null $message
     */
    private static function check(ConstraintViolationListInterface $violations, string $message = null)
    {
        if (count($violations) > 0) {
            throw new InvalidArgumentException($message);
        }
    }
}
