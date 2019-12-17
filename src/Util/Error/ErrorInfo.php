<?php


namespace Firebase\Util\Error;


class ErrorInfo
{
    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $message;

    public function __construct()
    {
        $args = func_get_args();

        if(count($args) === 1) {
            $this->code = $args[0]['code'];
            $this->message = $args[0]['message'];
        } else if (count($args) === 2) {
            $this->code = $args[0];
            $this->code = $args[1];
        }
    }
}