<?php


namespace Firebase\Auth;


use Firebase\FirebaseException;
use Firebase\Util\Validator\Validator;
use Throwable;

class FirebaseAuthException extends FirebaseException
{
    public function __construct($code, $message, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        Validator::isNonEmptyString($code);
    }
}
