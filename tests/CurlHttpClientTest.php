<?php

use ASG\DMRAPI\ApiClient;
use ASG\DMRAPI\Basic\CurlHttpClient;
use ASG\DMRAPI\HttpClientInterface;
use PHPUnit\Framework\TestCase as BaseTestCase;

final class CurlHttpClientTest extends BaseTestCase
{

    /**
     * @var HttpClientInterface|null
     */
    protected static $httpClient = null;

    /**
     * @beforeClass
     */
    public static function classSetup()
    {
        static::$httpClient = static::getHttpClientImplementation();
    }

    /**
     * @afterClass
     */
    public static function classTearDown()
    {
        static::$httpClient = null;
    }

    /**
     * @return CurlHttpClient
     */
    protected static function getHttpCLientImplementation()
    {
        return new CurlHttpClient();
    }

    /**
     * @return array
     */
    public function urlProvider()
    {
        return [
            [ApiClient::LIVE_URL . '/v1/version'],
            [ApiClient::STAGING_URL . '/v1/version'],
        ];
    }

    /**
     * @dataProvider urlProvider
     */
    public function testRequest($url)
    {
        $response = static::$httpClient->get($url, []);
        $this->assertTrue($response->isSuccessful(), 'Response was with code: ' . $response->getCode());
    }
}