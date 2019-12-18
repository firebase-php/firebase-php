<?php

namespace Firebase\Util\ApiRequest;


abstract class HttpResponse
{
    protected $status;

    protected $headers;

    protected $text;

    protected $data;

    /**
     * @var array | null
     */
    protected $multipart;

    abstract public function isJson(): bool;

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return HttpResponse
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param mixed $headers
     * @return HttpResponse
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     * @return HttpResponse
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return HttpResponse
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMultipart()
    {
        return $this->multipart;
    }

    /**
     * @param mixed $multipart
     * @return HttpResponse
     */
    public function setMultipart($multipart)
    {
        $this->multipart = $multipart;
        return $this;
    }
}