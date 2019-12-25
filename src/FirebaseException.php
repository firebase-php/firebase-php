<?php


namespace Firebase;


use Firebase\Util\Validator\Validator;
use Throwable;

class FirebaseException extends \Exception
{
    public function __construct($message = null, $code = null, Throwable $previous = null)
    {
        parent::__construct('', 0, $previous);
        $this->message = $message;
        $this->code = $code;
        Validator::isNonEmptyString($message, 'Detail message must not be empty');
    }
}
