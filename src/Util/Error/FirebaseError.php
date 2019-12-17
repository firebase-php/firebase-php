<?php


namespace Firebase\Util\Error;

class FirebaseError extends \Exception
{
    /**
     * @var ErrorInfo
     */
    private $errorInfo;

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
