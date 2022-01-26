<?php

namespace ASG\DMRAPI;

interface HttpClientInterface
{
    /**
     * @return string
     */
    public function getBaseUrl();

    /**
     * @return string
     */
    public function getUserAgent();

    /**
     * @param string $uri
     * @param array $headers
     * @return HttpResponseInterface
     */
    public function get($uri, array $headers);

    /**
     * @param string $uri
     * @param array $headers
     * @param string|null $data
     * @return HttpResponseInterface
     */
    public function post($uri, array $headers, $data = null);

    /**
     * @param string $uri
     * @param array $headers
     * @return HttpResponseInterface
     */
    public function delete($uri, array $headers);

    /**
     * @param string $uri
     * @param array $headers
     * @param string|null $data
     * @return HttpResponseInterface
     */
    public function put($uri, array $headers, $data = null);

    /**
     * @param string $uri
     * @param array $headers
     * @param string|null $data
     * @return HttpResponseInterface
     */
    public function patch($uri, array $headers, $data = null);
}