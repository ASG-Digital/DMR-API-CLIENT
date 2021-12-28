<?php

namespace ASG\DMRAPI;

class ApiClient
{
    const API_URL = 'https://dmr.asg-digital.dk/api';
    const VERSION1 = 'v1';

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var KeyPair|null
     */
    private $keyPair = null;

    /**
     * @param HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return HttpClientInterface
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @return KeyPair
     */
    public function getKeyPair()
    {
        return $this->keyPair;
    }

    /**
     * @param KeyPair $keyPair
     * @return void
     */
    public function setKeyPair(KeyPair $keyPair)
    {
        $this->keyPair = $keyPair;
    }

    /**
     * @param string $action
     * @param array $queryParams
     * @param string $version
     * @return string
     */
    protected function buildUrl($action, array $queryParams = [], $version = self::VERSION1)
    {
        $url = self::API_URL;
        $url .= '/' . rtrim($version , '/');
        $url .= '/' . trim($action, '/');
        if (!empty($queryParams)) {
            $url .=  '?' . http_build_query(
                $queryParams,
                    '',
                    null,
                    PHP_QUERY_RFC3986
            );
        }
        return $url;
    }

    /**
     * @return string
     */
    protected function getUserAgent()
    {
        return 'DMR-API-CLIENT: version:"' . DMR_API_CLIENT_VERSION . '", php:"' . phpversion() . '" via. (' .
            $this->getHttpClient()->getUserAgent() . ')';
    }

    /**
     * @return array
     */
    public function makeHeaders($hasPayload = false)
    {
        $h = [
            'User-Agent' => $this->getUserAgent(),
            'Accept' => 'application/json',
        ];
        if ($hasPayload) {
            $h['Content-Type'] = 'application/json';
        }
        return $h;
    }

    /**
     * @param string $clientKey
     * @param string $username
     * @param string $password
     * @return ApiResponse
     */
    public function login($clientKey, $username, $password)
    {
        $url = $this->buildUrl('login');
        $response = $this->getHttpClient()->post($url, $this->makeHeaders(true), json_encode([
            'client_key' => $clientKey,
            'username' => $username,
            'password' => $password,
        ]));
        $api = new ApiResponse($response);

        if ($api->isSuccessful()) {

        }
    }
}