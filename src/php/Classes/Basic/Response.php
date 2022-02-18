<?php

namespace ASG\DMRAPI\Basic;

use ASG\DMRAPI\HttpResponseInterface;

class Response implements HttpResponseInterface
{
    /**
     * @var int
     */
    private $code;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $uri;

    /**
     * @param int $code
     * @param array $headers
     * @param string $content
     * @param string $uri
     */
    public function __construct($code, array $headers, $content, $uri)
    {
        $this->code = $code;
        $this->headers = $headers;
        $this->content = $content;
        $this->uri = $uri;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function hasContent()
    {
        return !empty(trim($this->getContent()));
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return is_int($this->getCode()) && $this->getCode() >= 200 && $this->getCode() <= 299;
    }
}