<?php


namespace Firebase\Auth\Internal\HttpResponse;


class Error
{
    /**
     * @var string
     */
    private $code;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
}
