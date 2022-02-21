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

    public function checkInsurance(ApiResponse $response)
    {
        $this->assertTrue($response->isSuccessful(), 'Request Failed.');
        $this->assertTrue($response->hasContent(), 'Empty Data.');
        $this->assertTrue($response->has('company'), 'No "company" found.');
        $this->assertTrue(is_string($response->get('company')), '"company" is not a string value.');
        $this->assertTrue($response->has('status'), 'No "status" found.');
        $this->assertTrue(is_string($response->get('status')), '"status" is not a string value.');
        $this->assertTrue($response->has('created'), 'No "created" found.');
        $this->assertTrue(is_string($response->get('created')), '"created" is not a string value.');
    }

    public function checkInspection(ApiResponse $response)
    {
        $this->assertTrue($response->isSuccessful(), 'Request Failed.');
        $this->assertTrue($response->hasContent(), 'Empty Data.');
        $this->assertTrue($response->has('never_inspected'), '"never_inspected" not found.');
        $this->assertTrue($response->has('no_pending'), '"no_pending" not found.');
        $this->assertIsBool($response->get('never_inspected'), '"never_inspected" is not a bool value.');
        $this->assertIsBool($response->get('no_pending'), '"no_pending" is not a bool value.');
        $this->assertIsBool($response->has('latest'), 'has "latest" did not return bool value.');
        if ($response->has('latest.type')) {
            $this->assertIsString($response->get('latest.type'), '"latest.type" is not a string');
        }
        if ($response->has('latest.result')) {
            $this->assertIsString($response->get('latest.result'), '"latest.result" is not a string');
        }
        if ($response->has('latest.date')) {
            $this->assertIsString($response->get('latest.date'), '"latest.date" is not a string');
        }
    }

    public function checkEvaluations(ApiResponse $response)
    {
        $this->assertTrue($response->isSuccessful(), 'Request Failed.');
        $this->assertTrue($response->hasContent(), 'Empty Data.');
        $this->assertIsArray($response->getData(), 'Response data is not array.');
        $this->assertNotEmpty($response->getData(), 'Response data is empty.');
        foreach ($response->getData() as $evaluation) {
            $this->assertIsArray($evaluation, 'Evaluation unit is not array.');
            $this->assertNotEmpty($evaluation, 'Evaluation unit empty.');
            $this->assertArrayHasKey('evaluation_id', $evaluation, 'Evaluation unit missing id.');
            $this->assertIsInt($evaluation['evaluation_id'], 'Evaluation unit id is not a integer.');
            $this->assertArrayHasKey('evaluation_date', $evaluation, 'Evaluation unit missing date.');
            $this->assertArrayHasKey('vehicle', $evaluation, 'Evaluation unit missing vehicle.');
            $this->assertIsArray($evaluation['vehicle'], 'Evaluation unit\'s vehicle is not an array.');
            $this->assertArrayHasKey('id', $evaluation['vehicle'], 'Evaluation unit\'s vehicle is missing id.');
            $this->assertIsInt($evaluation['vehicle']['id'], 'Evaluation unit\'s vehicle id is not a integer.');
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

    /**
     * @depends testKeyPair
     * @dataProvider vehicleProvider
     */
    public function testVehicleInfoCachedLookup($type, $value, $make, $model, $year = null)
    {
        $response = static::$apiClient->vehicleInfo()->getVehicleCached($type, $value);
        $this->checkVehicle($response, $make, $model, $year);
    }

    /**
     * @depends testKeyPair
     * @dataProvider vehicleProvider
     */
    public function testVehicleInfoInsuranceScrapeLookup($type, $value, $make, $model, $year = null)
    {
        $response = static::$apiClient->vehicleInfo()->getInsurance($type, $value, true);
        $this->checkInsurance($response);
    }

    /**
     * @depends testKeyPair
     * @dataProvider vehicleProvider
     */
    public function testVehicleInfoInspectionScrapeLookup($type, $value, $make, $model, $year = null)
    {
        $response = static::$apiClient->vehicleInfo()->getInspection($type, $value, true);
        $this->checkInspection($response);
    }

    /**
     * @depends testKeyPair
     * @dataProvider vehicleProvider
     */
    public function testVehicleInfoEvaluationScrapeLookup($type, $value, $make, $model, $year = null)
    {
        $response = static::$apiClient->vehicleInfo()->getEvaluations($type, $value, true);
        $this->checkEvaluations($response);
    }
}
