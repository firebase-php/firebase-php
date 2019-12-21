<?php


namespace Firebase\Auth;


use Firebase\Auth\SessionCookieOptions\Builder;
use Firebase\Util\Error\AuthClientErrorCode;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAuthError;
use Firebase\Util\Validator\Validator;

class SessionCookieOptions
{
    /**
     * @var int
     */
    private $expiresIn;

    public function __construct(Builder $builder)
    {
        if(!Validator::isNumber($builder->getExpiresIn())) {
            $error = AuthClientErrorCode::INVALID_SESSION_COOKIE_DURATION;
            throw new FirebaseAuthError(
                new ErrorInfo($error['code'], $error['message'])
            );
        }
        $this->expiresIn = $builder->getExpiresIn();
    }
}
