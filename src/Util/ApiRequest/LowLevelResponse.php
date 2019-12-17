<?php

namespace Firebase\Util\ApiRequest;


class LowLevelResponse
{
    private $status;

    private $headers;

    private $request;

    private $data;

    private $multipart;

    /**
     * @var HttpRequestConfig
     */
    private $config;

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return LowLevelResponse
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
     * @return LowLevelResponse
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     * @return LowLevelResponse
     */
    public function setRequest($request)
    {
        $this->request = $request;
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
     * @return LowLevelResponse
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
     * @return LowLevelResponse
     */
    public function setMultipart($multipart)
    {
        $this->multipart = $multipart;
        return $this;
    }

    /**
     * @return HttpRequestConfig
     */
    public function getConfig(): HttpRequestConfig
    {
        return $this->config;
    }

    /**
     * @param HttpRequestConfig $config
     * @return LowLevelResponse
     */
    public function setConfig(HttpRequestConfig $config): LowLevelResponse
    {
        $this->config = $config;
        return $this;
    }
}
