<?php

namespace Firebase\Util\ApiRequest;


class HttpRequestConfig
{
    /**
     * @var HttpMethod
     */
    private $method;

    /**
     * @var string
     */
    private $url;

    /**
     * @var array | null
     */
    private $headers;

    /**
     * @var null | string | \stdClass
     */
    private $data;

    /**
     * @var int \ null
     */
    private $timeout;

    private $httpAgent;

    /**
     * @return HttpMethod
     */
    public function getMethod(): HttpMethod
    {
        return $this->method;
    }

    /**
     * @param HttpMethod $method
     * @return HttpRequestConfig
     */
    public function setMethod(HttpMethod $method): HttpRequestConfig
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return HttpRequestConfig
     */
    public function setUrl(string $url): HttpRequestConfig
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getHeaders(): ?array
    {
        return $this->headers;
    }

    /**
     * @param array|null $headers
     * @return HttpRequestConfig
     */
    public function setHeaders(?array $headers): HttpRequestConfig
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return \stdClass|string|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \stdClass|string|null $data
     * @return HttpRequestConfig
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     * @return HttpRequestConfig
     */
    public function setTimeout(int $timeout): HttpRequestConfig
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHttpAgent()
    {
        return $this->httpAgent;
    }

    /**
     * @param mixed $httpAgent
     * @return HttpRequestConfig
     */
    public function setHttpAgent($httpAgent)
    {
        $this->httpAgent = $httpAgent;
        return $this;
    }
}
