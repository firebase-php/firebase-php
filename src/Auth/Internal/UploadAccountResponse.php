<?php


namespace Firebase\Auth\Internal;


use Firebase\Auth\Internal\UploadAccountInfoResponse\ErrorInfo;

class UploadAccountResponse implements ResponseBuilder
{
    /**
     * @var ErrorInfo[]
     * @key error
     */
    private $errors;

    /**
     * @return ErrorInfo[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function build(array $content = null)
    {
        if(empty($content)) {
            return null;
        }
        $response = new static();
        $response->errors = [];
        if(is_array($content) && isset($content['error'])) {
            foreach($content['error'] as $error) {
                $response->errors = new ErrorInfo($error['index'], $error['message']);
            }
        }
        return $response;
    }
}
