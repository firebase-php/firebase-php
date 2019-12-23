<?php


namespace Firebase\Auth;


use Firebase\FirebaseException;
use Firebase\Util\Validator\Validator;
use Throwable;

class FirebaseAuthException extends FirebaseException
{
    public function __construct($code, $message, Throwable $previous = null)
    {
        if(is_null($previous)) {
            parent::__construct($message, $code);
        } else {
            parent::__construct($message, $code, $previous);
            Validator::isNonEmptyString($code);
        }
    }
}
