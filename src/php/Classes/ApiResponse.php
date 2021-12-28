<?php

namespace ASG\DMRAPI;

class ApiResponse implements HttpResponseInterface
{
    /**
     * @var HttpResponseInterface
     */
    private $httpResponse;

    public function __construct(HttpResponseInterface $httpResponse)
    {
        $this->httpResponse = $httpResponse;
    }

    /**
     * @return HttpResponseInterface
     */
    protected function getHttpResponse()
    {
        return $this->httpResponse;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return is_int($this->getCode()) && $this->getCode() >= 200 && $this->getCode() <= 299;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->httpResponse->getContent();
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->httpResponse->getCode();
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->httpResponse->getHeaders();
    }
}