<?php

namespace ASG\DMRAPI;

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
     * @return string[]
     */
    public function toArray()
    {
        return [
            'access_token' => $this->getAccessToken(),
            'refresh_token' => $this->getRefreshToken(),
        ];
    }

    public static function fromArray(array $tokens)
    {
        // TODO: Implement this.
    }
}