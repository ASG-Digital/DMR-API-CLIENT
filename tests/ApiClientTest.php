<?php

use ASG\DMRAPI\ApiClient;
use ASG\DMRAPI\ApiResponse;
use ASG\DMRAPI\Basic\CurlHttpClient;
use ASG\DMRAPI\Basic\JsonFileTokenStorage;
use ASG\DMRAPI\HttpClientInterface;
use ASG\DMRAPI\KeyPair;
use ASG\DMRAPI\Lookup;
use ASG\DMRAPI\TokenStorageInterface;
use PHPUnit\Framework\TestCase as BaseTestCase;

class ApiClientTest extends BaseTestCase
{
    /**
     * @var TokenStorageInterface|null
     */
    protected static $tokenStorage = null;

    /**
     * @var HttpClientInterface|null
     */
    protected static $httpClient = null;

    /**
     * @var ApiClient|null
     */
    protected static $apiClient = null;

    /**
     * @beforeClass
     */
    public static function classSetup()
    {
        static::$tokenStorage = static::getTokenStorageImplementation();
        static::$httpClient = static::getHttpCLientImplementation();
        static::$apiClient = new ApiClient(
            static::$httpClient,
            static::$tokenStorage,
            static::getBaseUrl()
        );
    }

    /**
     * @afterClass
     */
    public static function classTearDown()
    {
        static::$tokenStorage->reset();
        static::$tokenStorage = null;
        static::$httpClient = null;
        static::$apiClient = null;
    }

    /**
     * @return TokenStorageInterface
     */
    protected static function getTokenStorageImplementation()
    {
        return new JsonFileTokenStorage(dirname(__DIR__)  . '/tokens.json');
    }

    /**
     * @return HttpClientInterface
     */
    protected static function getHttpCLientImplementation()
    {
        return new CurlHttpClient();
    }

    /**
     * @return string
     */
    protected static function getBaseUrl()
    {
        $testRemote = getenv('TEST_REMOTE');
        if (
            !is_string($testRemote) || (
                stripos($testRemote, 'prod') === false &&
                stripos($testRemote, 'live') === false
            )
        ) {
            return ApiClient::STAGING_URL;
        }
        return ApiClient::LIVE_URL;
    }

    public function vehicleProvider()
    {
        return [
            [Lookup::ID, 9000000003850757, 'FORD', 'FIESTA', 2021],
            [Lookup::ID, 9000000000878020, 'VOLKSWAGEN', 'Golf 7', 2013],
            [Lookup::ID, 9000000003272821, 'HYUNDAI', 'I20', 2020],
            [Lookup::ID, 9000000002943204, 'VOLKSWAGEN', 'POLO', 2019],
            [Lookup::ID, 1008301200918243, 'FORD', 'FIESTA 5 DØRS', null],
            [Lookup::ID, 9000000003064814, 'SKODA', 'OCTAVIA', 2019],
            [Lookup::ID, 1010601199712215, 'HONDA', 'ACCORD', null],
            [Lookup::ID, 9000000004245896, 'BMW', 'X5', 2022],
            [Lookup::ID, 9000000002423713, 'FORD', 'FIESTA', 2017],
            [Lookup::ID, 1002601201111676, 'BMW', '1 SERIE', null],
            [Lookup::ID, 9000000003169113, 'TESLA', 'Model 3', 2019],
            [Lookup::ID, 9000000003722475, 'PORSCHE', 'Macan S', null],
        ];
    }

    public function testLogin()
    {
        $this->assertTrue(
            static::$apiClient->login(getenv('TEST_CLIENT_KEY'), getenv('TEST_USERNAME'), getenv('TEST_PASSWORD')),
            'Login failed.'
        );
    }

    /**
     * @depends testLogin
     */
    public function testRefresh()
    {
        $this->assertTrue(
            static::$apiClient->refreshTokens(),
            'Refresh failed.'
        );
    }

    /**
     * @depends testRefresh
     */
    public function testKeyPair()
    {
        $this->assertInstanceOf(KeyPair::class, static::$apiClient->getKeyPair());
    }

    public function checkVehicle(ApiResponse $response, $make, $model, $year = null)
    {
        $this->assertTrue($response->isSuccessful(), 'Request Failed.');
        $this->assertTrue($response->hasContent(), 'Empty Data.');
        $this->assertTrue($response->has('vehicle.designation.make'), 'Missing key: "vehicle.designation.make"');
        $this->assertEqualsIgnoringCase($make, $response->get('vehicle.designation.make'));
        $this->assertTrue($response->has('vehicle.designation.model'), 'Missing key: "vehicle.designation.model"');
        $this->assertEqualsIgnoringCase($model, $response->get('vehicle.designation.model'));
        if (is_int($year)) {
            $this->assertTrue($response->has('vehicle.model_year'), 'Missing key: "vehicle.model_year"');
            $this->assertEquals($year, $response->get('vehicle.model_year'));
        }
    }

    /**
     * @depends testKeyPair
     * @dataProvider vehicleProvider
     */
    public function testVehicleInfoForcedScrapeLookup($type, $value, $make, $model, $year = null)
    {
        $response = static::$apiClient->vehicleInfo()->getVehicle($type, $value, true);
        $this->checkVehicle($response, $make, $model, $year);
    }
}