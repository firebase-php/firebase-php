<?php


namespace Firebase\Auth\UserImportBuilder;


class UserImportResultErrorInfo
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var string
     */
    private $reason;

    /**
     * UserImportResultErrorInfo constructor.
     * @param int $index
     * @param string $reason
     */
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
