<?php

namespace ASG\DMRAPI;

class ApiResponse implements HttpResponseInterface
{
    /**
     * @var HttpResponseInterface
     */
    private $httpResponse;

    /**
     * @var bool|null
     */
    private $isJson = null;

    /**
     * @var bool|null
     */
    private $isXml = null;

    /**
     * @var array|null
     */
    private $data = null;

    /**
     * @param HttpResponseInterface $httpResponse
     */
    public function __construct(HttpResponseInterface $httpResponse)
    {
        $this->httpResponse = $httpResponse;
    }

    /**
     * @return HttpResponseInterface
     */
    protected function getHttpResponse()
    {
        return $this->httpResponse;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return is_int($this->getCode()) && $this->getCode() >= 200 && $this->getCode() <= 299;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->getHttpResponse()->getContent();
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->getHttpResponse()->getCode();
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->getHttpResponse()->getHeaders();
    }

    /**
     * @return array|null
     */
    public function getData()
    {
        if ($this->data === null) {
            if ($this->hasJsonHeader()) {
                $this->decodeJson();
            }
            if ($this->hasXmlHeader()) {
                $this->decodeXml();
            }
        }
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isJson()
    {
        if ($this->isJson !== null) {
            return $this->isJson;
        }
        return $this->hasJsonHeader() &&
            $this->decodeJson();
    }

    /**
     * @return bool
     */
    public function isXml()
    {
        if ($this->isXml !== null) {
            return $this->isXml;
        }
        return $this->hasXmlHeader() &&
            $this->decodeXml();
    }

    /**
     * @return bool
     */
    protected function hasJsonHeader()
    {
        return array_key_exists('Content-Type', $this->getHeaders()) &&
            $this->getHeaders()['Content-Type'] == 'application/json';
    }

    /**
     * @return bool
     */
    protected function hasXmlHeader()
    {
        return array_key_exists('Content-Type', $this->getHeaders()) &&
            $this->getHeaders()['Content-Type'] == 'application/xml';
    }

    /**
     * @return bool
     */
    protected function decodeJson()
    {
        $data = json_decode(
            $this->getContent(),
            true,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        if (json_last_error() == JSON_ERROR_NONE) {
            $this->data = $data;
            $this->isJson = true;
            $this->isXml = false;
            return true;
        }
        $this->isJson = false;
        return false;
    }

    /**
     * @return bool
     */
    protected function decodeXml()
    {
        $xml = simplexml_load_string(
            $this->getContent(),
            "SimpleXMLElement",
            LIBXML_NOCDATA
        );
        if ($xml === false) {
            $this->isXml = false;
            return false;
        }
        $json = json_encode($xml);
        if (json_last_error() != JSON_ERROR_NONE) {
            $this->isXml = false;
            return false;
        }
        $array = json_decode($json,true);
        if (json_last_error() != JSON_ERROR_NONE) {
            $this->isXml = false;
            return false;
        }
        $this->isXml = true;
        $this->isJson = true;
        $this->data = $array;
        return true;
    }

    /**
     * @param mixed $data
     * @return bool
     */
    protected function accessible($data)
    {
        return is_array($data) || $data instanceof \ArrayAccess;
    }

    /**
     * @param mixed $data
     * @param string|int|null $key
     * @return bool
     */
    protected function exists($data, $key)
    {
        if (!$this->accessible($data) || $key === null) {
            return false;
        }
        if ($data instanceof \ArrayAccess) {
            return $data->offsetExists($key);
        }
        return array_key_exists($key, $data);
    }

    /**
     * @param string|int|null $key
     * @return mixed
     */
    public function get($key)
    {
        $data = $this->getData();
        if (!$this->accessible($data)) {
            return null;
        }

        if ($key === null) {
            return $data;
        }

        if ($this->exists($data, $key)) {
            return $data[$key];
        }

        if (strpos($key, '.') === false) {
            return null;
        }

        foreach (explode('.', $key) as $part) {
            if ($this->exists($data, $part)) {
                $data = $data[$part];
            } else {
                return null;
            }
        }
        return $data;
    }

    /**
     * @param string[]|string $keys
     * @return bool
     */
    public function has($keys)
    {
        if (is_string($keys)) {
            $keys = [$keys];
        }

        if (!is_array($keys) || empty($keys)) {
            return false;
        }

        foreach ($keys as $key) {
            if ($this->exists(($data = $this->getData()), $key)) {
                continue;
            }

            foreach (explode('.', $key) as $subKey) {
                if ($this->exists($data, $subKey)) {
                    $data = $data[$subKey];
                } else {
                    return false;
                }
            }
        }
        return true;
    }
}