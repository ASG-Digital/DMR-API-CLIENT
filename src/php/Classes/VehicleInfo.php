<?php

namespace ASG\DMRAPI;

use ASG\DMRAPI\Basic\Response;
use ASG\DMRAPI\Exceptions\HttpClientException;
use ASG\DMRAPI\Exceptions\DmrApiException;

class VehicleInfo
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * @param ApiClient $apiClient
     */
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @return ApiClient
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * @param string $action
     * @param array $queryParams
     * @param string $version
     * @return string
     */
    protected function buildUrl($action, array $queryParams = [], $version = ApiClient::VERSION1)
    {
        $action = trim($action, " \t\n\r\0\x0B/");
        return $this->getApiClient()->buildUrl(
            trim('vehicle/info/' . $action, " \t\n\r\0\x0B/"),
            $queryParams,
            $version
        );
    }

    /**
     * @param $type
     * @return ApiResponse
     */
    public function getDistinct($type)
    {
        try {
            if (!in_array($type, DistinctType::VALID_TYPES)) {
                throw new DmrApiException('Invalid Distinct Value.');
            }
            $this->getApiClient()->requireAuth();
            $uri = $this->buildUrl('distinct/' . $type);
            return new ApiResponse($this->getApiClient()->getHttpClient()->get(
                $uri,
                $this->getApiClient()->makeHeaders(false)
            ));
        } catch (DmrApiException $dmrApiException) {
            return $this->makeErrorResponse($dmrApiException, (isset($uri) ? $uri : ''));
        } catch (HttpClientException $httpClientException) {
            return $this->makeErrorResponse($httpClientException, (isset($uri) ? $uri : ''));
        }
    }

    /**
     * @param string $lookup
     * @return void
     * @throws DmrApiException
     */
    protected function checkLookup($lookup)
    {
        if (!in_array($lookup, Lookup::VALID_TYPES)) {
            throw new DmrApiException('Invalid Lookup Value.');
        }
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @param bool $forceLiveData
     * @return ApiResponse
     */
    public function getVehicle($lookup, $value, $forceLiveData = false)
    {
        try {
            $this->checkLookup($lookup);
            $this->getApiClient()->requireAuth();
            $uri = $this->buildUrl('', [
                'type' => $lookup,
                'value' => $value,
                'force' => $forceLiveData,
            ]);
            return new ApiResponse($this->getApiClient()->getHttpClient()->get(
                $uri,
                $this->getApiClient()->makeHeaders(false)
            ));
        } catch (DmrApiException $dmrApiException) {
            return $this->makeErrorResponse($dmrApiException, (isset($uri) ? $uri : ''));
        } catch (HttpClientException $httpClientException) {
            return $this->makeErrorResponse($httpClientException, (isset($uri) ? $uri : ''));
        }
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @return ApiResponse
     */
    public function getVehicleCached($lookup, $value)
    {
        try {
            $this->checkLookup($lookup);
            $this->getApiClient()->requireAuth();
            $uri = $this->buildUrl('cached', [
                'type' => $lookup,
                'value' => $value,
            ]);
            return new ApiResponse($this->getApiClient()->getHttpClient()->get(
                $uri,
                $this->getApiClient()->makeHeaders(false)
            ));
        } catch (DmrApiException $dmrApiException) {
            return $this->makeErrorResponse($dmrApiException, (isset($uri) ? $uri : ''));
        } catch (HttpClientException $httpClientException) {
            return $this->makeErrorResponse($httpClientException, (isset($uri) ? $uri : ''));
        }
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @return ApiResponse
     * @deprecated
     */
    public function getCompleteVehicle($lookup, $value)
    {
        try {
            $this->checkLookup($lookup);
            $this->getApiClient()->requireAuth();
            $uri = $this->buildUrl('complete', [
                'type' => $lookup,
                'value' => $value,
            ]);
            return new ApiResponse($this->getApiClient()->getHttpClient()->get(
                $uri,
                $this->getApiClient()->makeHeaders(false)
            ));
        } catch (DmrApiException $dmrApiException) {
            return $this->makeErrorResponse($dmrApiException, (isset($uri) ? $uri : ''));
        } catch (HttpClientException $httpClientException) {
            return $this->makeErrorResponse($httpClientException, (isset($uri) ? $uri : ''));
        }
    }

    /**
     * @param Filter[] $filters
     * @param int $offset
     * @param int $limit
     * @return ApiResponse
     * @todo Change this to use POST instead of GET
     */
    public function getVehicleList(array $filters = [], $offset = 0, $limit = 199)
    {
        try {
            if ((($limit - $offset) + 1) > 1000) {
                throw new DmrApiException('Range to high, range size must not exceed 1000.');
            }
            $this->getApiClient()->requireAuth();
            $f = [];
            array_walk($filters, function (Filter $item, $key) use(&$f) {
                $f[$key] = [
                    0 => $item->getField(),
                    1 => $item->getOperator(),
                    2 => $item->getValue(),
                ];
            });
            $headers = $this->getApiClient()->makeHeaders(false);
            $headers['Range'] = 'vehicle=' .  $offset . '-' . $limit;
            $uri = $this->buildUrl('list', ['f' => $f]);
            return new ApiResponse($this->getApiClient()->getHttpClient()->get(
                $uri,
                $headers
            ));
        } catch (DmrApiException $dmrApiException) {
            return $this->makeErrorResponse($dmrApiException, (isset($uri) ? $uri : ''));
        } catch (HttpClientException $httpClientException) {
            return $this->makeErrorResponse($httpClientException, (isset($uri) ? $uri : ''));
        }
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @return ApiResponse
     */
    public function getEvaluations($lookup, $value)
    {
        try {
            $this->checkLookup($lookup);
            $this->getApiClient()->requireAuth();
            $uri = $this->buildUrl('evaluation', [
                'type' => $lookup,
                'value' => $value,
            ]);
            return new ApiResponse($this->getApiClient()->getHttpClient()->get(
                $uri,
                $this->getApiClient()->makeHeaders(false)
            ));
        } catch (DmrApiException $dmrApiException) {
            return $this->makeErrorResponse($dmrApiException, (isset($uri) ? $uri : ''));
        } catch (HttpClientException $httpClientException) {
            return $this->makeErrorResponse($httpClientException, (isset($uri) ? $uri : ''));
        }
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @param bool $forceLiveData
     * @return ApiResponse
     */
    public function getInspection($lookup, $value, $forceLiveData = false)
    {
        try {
            $this->checkLookup($lookup);
            $this->getApiClient()->requireAuth();
            $uri = $this->buildUrl('inspection', [
                'type' => $lookup,
                'value' => $value,
                'force' => $forceLiveData,
            ]);
            return new ApiResponse($this->getApiClient()->getHttpClient()->get(
                $uri,
                $this->getApiClient()->makeHeaders(false)
            ));
        } catch (DmrApiException $dmrApiException) {
            return $this->makeErrorResponse($dmrApiException, (isset($uri) ? $uri : ''));
        } catch (HttpClientException $httpClientException) {
            return $this->makeErrorResponse($httpClientException, (isset($uri) ? $uri : ''));
        }
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @param bool $forceLiveData
     * @return ApiResponse
     */
    public function getInspectionHistory($lookup, $value, $forceLiveData = false)
    {
        try {
            $this->checkLookup($lookup);
            $this->getApiClient()->requireAuth();
            $uri = $this->buildUrl('inspection/history', [
                'type' => $lookup,
                'value' => $value,
                'force' => $forceLiveData,
            ]);
            return new ApiResponse($this->getApiClient()->getHttpClient()->get(
                $uri,
                $this->getApiClient()->makeHeaders(false)
            ));
        } catch (DmrApiException $dmrApiException) {
            return $this->makeErrorResponse($dmrApiException, (isset($uri) ? $uri : ''));
        } catch (HttpClientException $httpClientException) {
            return $this->makeErrorResponse($httpClientException, (isset($uri) ? $uri : ''));
        }
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @param bool $forceLiveData
     * @return ApiResponse
     */
    public function getInsurance($lookup, $value, $forceLiveData = false)
    {
        try {
            $this->checkLookup($lookup);
            $this->getApiClient()->requireAuth();
            $uri = $this->buildUrl('insurance', [
                'type' => $lookup,
                'value' => $value,
                'force' => $forceLiveData,
            ]);
            return new ApiResponse($this->getApiClient()->getHttpClient()->get(
                $uri,
                $this->getApiClient()->makeHeaders(false)
            ));
        } catch (DmrApiException $dmrApiException) {
            return $this->makeErrorResponse($dmrApiException, (isset($uri) ? $uri : ''));
        } catch (HttpClientException $httpClientException) {
            return $this->makeErrorResponse($httpClientException, (isset($uri) ? $uri : ''));
        }
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @param bool $forceLiveData
     * @return ApiResponse
     */
    public function getInsuranceHistory($lookup, $value, $forceLiveData = false)
    {
        try {
            $this->checkLookup($lookup);
            $this->getApiClient()->requireAuth();
            $uri = $this->buildUrl('insurance/history', [
                'type' => $lookup,
                'value' => $value,
                'force' => $forceLiveData
            ]);
            return new ApiResponse($this->getApiClient()->getHttpClient()->get(
                $uri,
                $this->getApiClient()->makeHeaders(false)
            ));
        } catch (DmrApiException $dmrApiException) {
            return $this->makeErrorResponse($dmrApiException, (isset($uri) ? $uri : ''));
        } catch (HttpClientException $httpClientException) {
            return $this->makeErrorResponse($httpClientException, (isset($uri) ? $uri : ''));
        }
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @param bool $force
     * @return ApiResponse
     */
    public function queueFullScrape($lookup, $value, $force = false)
    {
        try {
            $this->checkLookup($lookup);
            $this->getApiClient()->requireAuth();
            $uri = $this->buildUrl('queue', [
                'type' => $lookup,
                'value' => $value,
                'force' => ($force ? 'true' : 'false'),
            ]);
            return new ApiResponse($this->getApiClient()->getHttpClient()->get(
                $uri,
                $this->getApiClient()->makeHeaders(false)
            ));
        } catch (DmrApiException $dmrApiException) {
            return $this->makeErrorResponse($dmrApiException, (isset($uri) ? $uri : ''));
        } catch (HttpClientException $httpClientException) {
            return $this->makeErrorResponse($httpClientException, (isset($uri) ? $uri : ''));
        }
    }

    /**
     * @param \Exception $exception
     * @param string $uri
     * @return ApiResponse
     */
    private function makeErrorResponse($exception, $uri = '')
    {
        return new ApiResponse(new Response(400, [
            'content-type' => 'application/json'
        ], json_encode([
            'message' => $exception->getMessage(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $uri), $exception);
    }
}