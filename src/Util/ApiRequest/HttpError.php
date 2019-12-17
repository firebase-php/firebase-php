<?php

namespace Firebase\Util\ApiRequest;


class HttpError extends \Exception
{
    public function __construct(HttpResponse $response)
    {
        parent::__construct('Server responded with status' . $response->getStatus());
    }

}
