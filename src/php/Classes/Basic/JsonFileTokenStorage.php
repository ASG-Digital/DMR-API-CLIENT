<?php

namespace ASG\DMRAPI\Basic;

use ASG\DMRAPI\Exceptions\DmrApiException;
use ASG\DMRAPI\KeyPair;
use ASG\DMRAPI\TokenStorageInterface;

class JsonFileTokenStorage implements TokenStorageInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @param KeyPair $keyPair
     * @return void
     */
    public function write(KeyPair $keyPair)
    {
        if (!file_exists(dirname($this->filePath))) {
            mkdir(dirname($this->filePath), 0777, true);
        }
        file_put_contents(
            $this->filePath,
            json_encode($keyPair->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * @return KeyPair|null
     * @throws DmrApiException
     */
    public function read()
    {
        if (!file_exists($this->filePath)) {
            return null;
        }
        if (($content = file_get_contents($this->filePath)) === false) {
            return null;
        }
        $data = json_decode($content, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            unlink($this->filePath);
            return null;
        }
        return KeyPair::fromArray($data);
    }

    /**
     * @return void
     */
    public function reset()
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }
}