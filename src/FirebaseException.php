<?php


namespace Firebase;


use Firebase\Util\Validator\Validator;
use Throwable;

class FirebaseException extends \Exception
{
    public function __construct(?string $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        Validator::isNonEmptyString($message, 'Detail message must not be empty');
    }
}
