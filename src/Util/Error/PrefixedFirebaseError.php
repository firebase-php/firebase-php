<?php


namespace Firebase\Util\Error;


class PrefixedFirebaseError extends FirebaseError
{
    /**
     * @var string
     */
    private $codePrefix;

    public function __construct(string $codePrefix, string $code, string $message)
    {
        $this->codePrefix = $codePrefix;
        $this->code = "$codePrefix/$code";
        $errorInfo = new ErrorInfo();
        $errorInfo->code = $this->code;
        $errorInfo->message = $this->message;
        parent::__construct($errorInfo);
    }

    public function hasCode(string $code): bool {
        return "$this->codePrefix/$code" === $this->code;
    }
}
