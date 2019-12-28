<?php


namespace Firebase\Auth\Internal\UploadAccountInfoResponse;

class ErrorInfo
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var string
     */
    private $message;

    public function __construct(int $index, string $message)
    {
        $this->index = $index;
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
