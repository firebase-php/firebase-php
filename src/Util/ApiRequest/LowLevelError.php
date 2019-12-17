<?php

namespace Firebase\Util\ApiRequest;


class LowLevelError extends \Exception
{
    private $config;

    private $request;

    /**
     * @var
     */
    private $response;
}