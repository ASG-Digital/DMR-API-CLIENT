<?php

namespace ASG\DMRAPI;

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
     * @throws DmrApiException
     */
    public function getDistinct($type)
    {
        if (!in_array($type, DistinctType::VALID_TYPES)) {
            throw new DmrApiException('Invalid Distinct Value.');
        }
        $this->getApiClient()->requireAuth();
        $uri = $this->buildUrl('distinct/' . $type);
        return new ApiResponse($this->getApiClient()->getHttpClient()->get(
            $uri,
            $this->getApiClient()->makeHeaders(false)
        ));
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
     * @return ApiResponse
     * @throws DmrApiException
     */
    public function getVehicle($lookup, $value)
    {
        $this->checkLookup($lookup);
        $this->getApiClient()->requireAuth();
        $uri = $this->buildUrl('', [
            'type' => $lookup,
            'value' => $value,
        ]);
        return new ApiResponse($this->getApiClient()->getHttpClient()->get(
            $uri,
            $this->getApiClient()->makeHeaders(false)
        ));
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @return ApiResponse
     * @throws DmrApiException
     */
    public function getCompleteVehicle($lookup, $value)
    {
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
    }

    /**
     * @param Filter[] $filters
     * @param int $offset
     * @param int $limit
     * @return ApiResponse
     * @throws DmrApiException
     */
    public function getVehicleList(array $filters = [], $offset = 0, $limit = 199)
    {
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
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @return ApiResponse
     * @throws DmrApiException
     */
    public function getEvaluations($lookup, $value)
    {
        $this->checkLookup($lookup);
        $this->getApiClient()->requireAuth();
        return new ApiResponse($this->getApiClient()->getHttpClient()->get(
            $this->buildUrl('evaluation', [
                'type' => $lookup,
                'value' => $value,
            ]),
            $this->getApiClient()->makeHeaders(false)
        ));
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @return ApiResponse
     * @throws DmrApiException
     */
    public function getInspections($lookup, $value)
    {
        $this->checkLookup($lookup);
        $this->getApiClient()->requireAuth();
        return new ApiResponse($this->getApiClient()->getHttpClient()->get(
            $this->buildUrl('inspection', [
                'type' => $lookup,
                'value' => $value,
            ]),
            $this->getApiClient()->makeHeaders(false)
        ));
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @return ApiResponse
     * @throws DmrApiException
     */
    public function getInspectionHistory($lookup, $value)
    {
        $this->checkLookup($lookup);
        $this->getApiClient()->requireAuth();
        return new ApiResponse($this->getApiClient()->getHttpClient()->get(
            $this->buildUrl('inspection/history', [
                'type' => $lookup,
                'value' => $value,
            ]),
            $this->getApiClient()->makeHeaders(false)
        ));
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @return ApiResponse
     * @throws DmrApiException
     */
    public function getInsurance($lookup, $value)
    {
        $this->checkLookup($lookup);
        $this->getApiClient()->requireAuth();
        return new ApiResponse($this->getApiClient()->getHttpClient()->get(
            $this->buildUrl('insurance', [
                'type' => $lookup,
                'value' => $value,
            ]),
            $this->getApiClient()->makeHeaders(false)
        ));
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @return ApiResponse
     * @throws DmrApiException
     */
    public function getInsuranceHistory($lookup, $value)
    {
        $this->checkLookup($lookup);
        $this->getApiClient()->requireAuth();
        return new ApiResponse($this->getApiClient()->getHttpClient()->get(
            $this->buildUrl('insurance/history', [
                'type' => $lookup,
                'value' => $value,
            ]),
            $this->getApiClient()->makeHeaders(false)
        ));
    }

    /**
     * @param string $lookup
     * @param string|int $value
     * @param bool $force
     * @return ApiResponse
     * @throws DmrApiException
     */
    public function queueFullScrape($lookup, $value, $force = false)
    {
        $this->checkLookup($lookup);
        $this->getApiClient()->requireAuth();
        return new ApiResponse($this->getApiClient()->getHttpClient()->get(
            $this->buildUrl('insurance/history', [
                'type' => $lookup,
                'value' => $value,
                'force' => ($force ? 'true' : 'false'),
            ]),
            $this->getApiClient()->makeHeaders(false)
        ));
    }
}