<?php


namespace Firebase\Auth;

final class ErrorInfo
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var string
     */
    private $reason;

    public function __construct(int $index, string $reason)
    {
        $this->index = $index;
        $this->reason = $reason;
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
    public function getReason(): string
    {
        return $this->reason;
    }
}
