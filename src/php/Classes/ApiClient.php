<?php

namespace ASG\DMRAPI;

use ASG\DMRAPI\Exceptions\DmrApiException;

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
     * @return void
     * @throws DmrApiException
     */
    public function login($clientKey, $username, $password)
    {
        try {
            $url = $this->buildUrl('login');
            $response = $this->getHttpClient()->post($url, $this->makeHeaders(true), json_encode([
                'client_key' => $clientKey,
                'username' => $username,
                'password' => $password,
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            $api = new ApiResponse($response);
            if (!$api->isSuccessful()) {
                throw new DmrApiException('Login Failed', $api->getCode());
            }
            if (
                !$api->has(['data.access_token', 'data.refresh_token']) ||
                !is_string($api->get('data.access_token')) ||
                !is_string($api->get('data.refresh_token'))
            ) {
                throw new DmrApiException('Login Failed', 502);
            }
            $this->keyPair = new KeyPair($api->get('data.access_token'), $api->get('data.refresh_token'));
            return;
        } catch (\Exception $exception) {
            if ($exception instanceof DmrApiException) {
                throw $exception;
            }
            throw new DmrApiException('Login Failed', -1, $exception);
        }
    }

    public function refreshTokens()
    {
        if (!($this->getKeyPair() instanceof KeyPair)) {
            throw new DmrApiException('Cannot refresh without a valid KeyPair.');
        }

    }
}