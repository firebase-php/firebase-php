<?php


namespace Firebase\Util\Error;


class ErrorInfo
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string | null
     */
    private $message;

    public function __construct(string $code, string $message = null)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }
}