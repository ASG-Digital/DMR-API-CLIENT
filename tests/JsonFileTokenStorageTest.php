<?php

use ASG\DMRAPI\Basic\JsonFileTokenStorage;
use ASG\DMRAPI\KeyPair;
use ASG\DMRAPI\TokenStorageInterface;
use PHPUnit\Framework\TestCase as BaseTestCase;

final class JsonFileTokenStorageTest extends BaseTestCase
{
    /**
     * @var TokenStorageInterface|null
     */
    protected static $tokenStorage = null;

    /**
     * @beforeClass
     */
    public static function classSetup()
    {
        static::$tokenStorage = static::getTokenStorageImplementation();
    }

    /**
     * @afterClass
     */
    public static function classTearDown()
    {
        static::$tokenStorage->reset();
        static::$tokenStorage = null;
    }

    /**
     * @return JsonFileTokenStorage
     */
    public static function getTokenStorageImplementation()
    {
        return new JsonFileTokenStorage(dirname(__DIR__)  . '/tokens.json');
    }

    public function tokensProvider()
    {
        return [
            ['test1234', '4321test'],
        ];
    }

    public function keysProvider()
    {
        return [
            [
                KeyPair::fromArray([
                    'access_token' => 'test1234',
                    'refresh_token' => '4321test',
                ])
            ],
        ];
    }

    public function testKeyCreationFailed()
    {
        $this->expectException(\ASG\DMRAPI\Exceptions\DmrApiException::class);
        KeyPair::fromArray([]);
    }

    /**
     * @dataProvider tokensProvider
     */
    public function testKeyCreationSuccessful($accessToken, $refreshToken)
    {
        $obj = KeyPair::fromArray([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ]);
        $this->assertInstanceOf(KeyPair::class, $obj);
    }

    /**
     * @depends testKeyCreationSuccessful
     * @dataProvider keysProvider
     */
    public function testStorage(KeyPair $keyPair)
    {
        static::$tokenStorage->write($keyPair);
        $this->assertEquals(
            $keyPair->toArray(),
            static::$tokenStorage->read()->toArray(),
            'Read array is not equal to the Written array',
            0.0,
            10,
            true
        );
    }
}