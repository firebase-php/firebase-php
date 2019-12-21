<?php


namespace Firebase\Util\Error;


class ErrorInfo
{
    /**
     * @var string
     */
    public $code;

    /**
     * @var string | null
     */
    public $message;

    public function __construct(string $code, string $message = null)
    {
        $this->code = $code;
        $this->message = $message;
    }
}