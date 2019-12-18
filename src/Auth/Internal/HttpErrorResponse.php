<?php


namespace Firebase\Auth\Internal;


use Firebase\Auth\Internal\HttpResponse\Error;
use Respect\Validation\Validator as v;

class HttpErrorResponse
{
    /**
     * @var Error
     */
    private $error;

    public function getErrorCode(): string {
        if(!is_null($this->error)) {
            if(v::stringType()->notEmpty()->validate($this->error->getCode())) {
                return $this->error->getCode();
            }
        }

        return 'unknown';
    }
}
