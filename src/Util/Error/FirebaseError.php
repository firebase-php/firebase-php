<?php


namespace Firebase\Util\Error;


use Throwable;

class FirebaseError extends \Exception
{
    private ErrorInfo $errorInfo;

    public function __construct(ErrorInfo $errorInfo)
    {
        $this->errorInfo = $errorInfo;
        parent::__construct($errorInfo->message, $errorInfo->code);
    }

    public function toJSON(): array {
        return [
            'code' => $this->errorInfo->code,
            'message' => $this->errorInfo->message
        ];
    }
}
