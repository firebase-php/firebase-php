<?php

namespace Firebase\Util\ApiRequest;


use MyCLabs\Enum\Enum;

class HttpMethod extends Enum
{
    private const GET = 'GET';

    private const POST = 'POST';

    private const PUT = 'PUT';

    private const DELETE = 'DELETE';

    private const PATCH = 'PATCH';

    private const HEAD = 'HEAD';
}
