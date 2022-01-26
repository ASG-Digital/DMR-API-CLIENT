<?php

namespace ASG\DMRAPI;

use ASG\DMRAPI\Exceptions\DmrApiException;

class ApiClient
{
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
     * @var VehicleInfo|null
     */
    private $vehicleInfo = null;

    /**
     * @var TokenStorageInterface|null
     */
    private $tokenStorage;

    /**
     * @param HttpClientInterface $httpClient
     * @param TokenStorageInterface|null $tokenStorage
     */
    public function __construct(HttpClientInterface $httpClient, TokenStorageInterface $tokenStorage = null)
    {
        $this->httpClient = $httpClient;
        $this->tokenStorage = $tokenStorage;
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
        if ($this->keyPair === null && $this->tokenStorage !== null) {
            $fromStorage = $this->tokenStorage->read();
            if ($fromStorage instanceof KeyPair) {
                $this->keyPair = $fromStorage;
            }
        }
        return $this->keyPair;
    }

    /**
     * @param string $action
     * @param string[] $queryParams
     * @param string $version
     * @return string
     */
    public function buildUrl($action, array $queryParams = [], $version = self::VERSION1)
    {
        $url = rtrim($this->getHttpClient()->getBaseUrl(), " \t\n\r\0\x0B/");
        $url .= '/' . rtrim($version, '/');
        $url .= '/' . trim($action, '/');
        if (!empty($queryParams)) {
            $url .=  '?' . http_build_query(
                $queryParams,
                '',
                '&',
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
        if ($this->getKeyPair() instanceof KeyPair) {
            $h['Authorization'] = 'Bearer ' . $this->getKeyPair()->getAccessToken();
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
            $this->handleLoginResponse($this->getHttpClient()->post($url, $this->makeHeaders(true), json_encode([
                'client_key' => $clientKey,
                'username' => $username,
                'password' => $password,
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)));
        } catch (\Exception $exception) {
            if ($exception instanceof DmrApiException) {
                throw $exception;
            }
            throw new DmrApiException('Login Failed', -1, $exception);
        }
    }

    /**
     * @return void
     * @throws DmrApiException
     */
    public function refreshTokens()
    {
        if (!($this->getKeyPair() instanceof KeyPair)) {
            throw new DmrApiException('Cannot refresh without a valid KeyPair.');
        }
        try {
            $url = $this->buildUrl('login/refresh');
            $this->handleLoginResponse($this->getHttpClient()->post($url, $this->makeHeaders(true), json_encode([
                'refresh_token' => $this->getKeyPair()->getRefreshToken(),
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)));
        } catch (\Exception $exception) {
            if ($exception instanceof DmrApiException) {
                throw $exception;
            }
            throw new DmrApiException('Login Failed', -1, $exception);
        }
    }

    /**
     * @param HttpResponseInterface $response
     * @return void
     * @throws DmrApiException
     */
    private function handleLoginResponse(HttpResponseInterface $response)
    {
        try {
            $api = new ApiResponse($response);
            if (!$api->isSuccessful()) {
                throw new DmrApiException(
                    'Login Failed' . ($api->hasMessage() ? ' With message: "' . $api->getMessage() . '".' : ''),
                    $api->getCode()
                );
            }
            if (
                !$api->has(['access_token', 'refresh_token']) ||
                !is_string($api->get('access_token')) ||
                !is_string($api->get('refresh_token'))
            ) {
                throw new DmrApiException('Login Failed with invalid response.', 502);
            }
            $this->keyPair = new KeyPair($api->get('access_token'), $api->get('refresh_token'));
            if ($this->tokenStorage !== null) {
                $this->tokenStorage->write($this->keyPair);
            }
            return;
        } catch (\Exception $exception) {
            if ($exception instanceof DmrApiException) {
                throw $exception;
            }
            throw new DmrApiException('Login Failed', -1, $exception);
        }
    }

    /**
     * Call this to require authentication for the subsequent calls
     * @return void
     * @throws DmrApiException
     * @internal
     */
    public function requireAuth()
    {
        if (!($this->getKeyPair() instanceof KeyPair)) {
            throw new DmrApiException('Invalid method call. Missing authentication.');
        }
        $data = $this->getKeyPair()->getDecodedAccessToken();
        try {
            if (is_array($data) && array_key_exists('exp', $data) && is_numeric($data['exp'])) {
                $exp = \DateTimeImmutable::createFromFormat('U', $data['exp'], new \DateTimeZone('UTC'));
                $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
                if ($now >= $exp) {
                    $this->refreshTokens();
                }
            }
        } catch (\Exception $exception) {
            if ($exception instanceof DmrApiException) {
                throw $exception;
            }
            throw new DmrApiException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @return VehicleInfo
     */
    public function vehicleInfo()
    {
        if ($this->vehicleInfo === null) {
            $this->vehicleInfo = new VehicleInfo($this);
        }
        return $this->vehicleInfo;
    }
}