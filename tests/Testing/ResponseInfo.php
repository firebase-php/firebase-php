<?php


namespace Firebase\Tests\Testing;


use Psr\Http\Message\ResponseInterface;

class ResponseInfo
{
    private $status;

    private $payload;

    public function __construct(ResponseInterface $response)
    {
        $this->status = $response->getStatusCode();
        $this->payload = $response->getBody();
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getPayload(): \Psr\Http\Message\StreamInterface
    {
        return $this->payload;
    }
}
