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
     * @param int $code
     * @param array $headers
     * @param string $content
     */
    public function __construct($code, array $headers, $content)
    {
        $this->code = $code;
        $this->headers = $headers;
        $this->content = $content;
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
}