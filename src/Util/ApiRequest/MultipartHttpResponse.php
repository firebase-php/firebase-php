<?php


namespace Firebase\Util\ApiRequest;


use Firebase\Util\Error\AppErrorCodes;
use Firebase\Util\Error\ErrorInfo;
use Firebase\Util\Error\FirebaseAppError;

class MultipartHttpResponse extends HttpResponse
{
    public function __construct(LowLevelResponse $resp)
    {
        $this->status = $resp->getStatus();
        $this->headers = $resp->getHeaders();
        $this->multipart = $resp->getMultipart();
    }

    public function getText()
    {
        throw new FirebaseAppError(
            new ErrorInfo(AppErrorCodes::UNABLE_TO_PARSE_RESPONSE),
            'Unable to parse multipart payload as text'
        );
    }

    public function getData()
    {
        throw new FirebaseAppError(
            new ErrorInfo(AppErrorCodes::UNABLE_TO_PARSE_RESPONSE),
            'Unable to parse multipart payload as JSON'
        );
    }

    public function isJson(): bool
    {
        return false;
    }
}
