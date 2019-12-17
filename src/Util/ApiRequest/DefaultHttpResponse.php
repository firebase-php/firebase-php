<?php

namespace Firebase\Util\ApiRequest;


use Firebase\Util\Error\AppErrorCodes;
use Firebase\Util\Error\FirebaseAppError;

class DefaultHttpResponse extends HttpResponse
{
    private $parsedData;

    /**
     * @var \Exception
     */
    private $parseError;

    private $request;

    public function __construct(LowLevelResponse $resp)
    {
        $this->status = $resp->getStatus();
        $this->headers = $resp->getHeaders();
        $this->text = $resp->getData();

        try {
            if(!$resp->getData()) {
                throw new FirebaseAppError(AppErrorCodes::INTERNAL_ERROR, 'HTTP response missing data');
            }
            $this->parsedData = json_decode($resp->getData());
        } catch (\Exception $e) {
            $this->parsedData = null;
            $this->parseError = $e;
        }
        $this->request = $resp->getConfig()->getMethod() . ' ' . $resp->getConfig()->getUrl();
    }

    public function getData() {
        if($this->isJson()) {
            return $this->parsedData;
        }

        throw new FirebaseAppError(
            AppErrorCodes::UNABLE_TO_PARSE_RESPONSE,
            'Error while parsing response data: "' . $this->parseError->__toString() . '". Raw server response:"' . $this->text . '". Status code: "' . $this->status . '". Outgoing request: "' . $this->request . '".'
        );
    }


    public function isJson(): bool
    {
        return isset($this->parsedData);
    }

}