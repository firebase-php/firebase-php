<?php


namespace Firebase\Auth\Internal;


use Firebase\Auth\Internal\UploadAccountInfoResponse\ErrorInfo;

class UploadAccountResponse
{
    /**
     * @var ErrorInfo[]
     */
    private $errors;

    /**
     * @return ErrorInfo[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
