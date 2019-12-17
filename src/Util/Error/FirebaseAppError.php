<?php


namespace Firebase\Util\Error;


class FirebaseAppError extends PrefixedFirebaseError
{
    public function __construct(string $code, string $message)
    {
        parent::__construct('app', $code, $message);
    }
}
