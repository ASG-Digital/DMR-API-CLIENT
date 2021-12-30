<?php

namespace ASG\DMRAPI;

use ASG\DMRAPI\Exceptions\DmrApiException;

class KeyPair
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var array|null
     */
    private $decodedAccessToken = null;

    /**
     * @param string $accessToken
     * @param string $refreshToken
     */
    public function __construct($accessToken, $refreshToken)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @return array
     * @throws DmrApiException
     */
    public function getDecodedAccessToken()
    {
        if ($this->decodedAccessToken === null) {
            if (
                substr_count($this->getAccessToken(), '.') !== 2 ||
                count($parts = explode('.', $this->getAccessToken(), 3)) !== 3
            ) {
                throw new DmrApiException('JWT validation failed.');
            }
            $payload = urlsafeB64Decode($parts[array_keys($parts)[1]]);
            $data = json_decode($payload, true);
            if (json_last_error() != JSON_ERROR_NONE) {
                throw new DmrApiException(
                    'JWT json decode failed(' . json_last_error() . ') : ' . json_last_error_msg()
                );
            }
            $this->decodedAccessToken = $data;
        }
        return $this->decodedAccessToken;
    }

    /**
     * @return string[]
     */
    public function toArray()
    {
        return [
            'access_token' => $this->getAccessToken(),
            'refresh_token' => $this->getRefreshToken(),
        ];
    }

    /**
     * @param array $tokens
     * @return KeyPair
     * @throws DmrApiException
     */
    public static function fromArray(array $tokens)
    {
        if (
            !array_key_exists('access_token', $tokens) ||
            !array_key_exists('refresh_token', $tokens) ||
            !is_string($tokens['access_token']) ||
            !is_string($tokens['refresh_token'])
        ) {
            throw new DmrApiException('Failed to init KeyPair from Invalid Array.');
        }
        return new KeyPair($tokens['access_token'], $tokens['refresh_token']);
    }
}