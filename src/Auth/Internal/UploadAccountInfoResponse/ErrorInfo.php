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
