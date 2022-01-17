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
     * @var int|null
     */
    private $offset = null;

    /**
     * @var int|null
     */
    private $limit = null;

    /**
     * @var int|null
     */
    private $count = null;

    /**
     * @var string|null
     */
    private $unit = null;

    /**
     * @var \Exception|null
     */
    private $exception;

    /**
     * @param HttpResponseInterface $httpResponse
     * @param \Exception|null $exception
     */
    public function __construct(HttpResponseInterface $httpResponse, \Exception $exception = null)
    {
        $this->httpResponse = $httpResponse;
        $this->exception = $exception;
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
     * @return bool
     */
    public function hasContent()
    {
        return $this->getHttpResponse()->hasContent();
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
        return array_change_key_case($this->getHttpResponse()->getHeaders(), CASE_LOWER);
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->getHttpResponse()->getUri();
    }

    /**
     * @return bool
     */
    public function hasException()
    {
        return $this->exception instanceof \Exception;
    }

    /**
     * @return \Exception|null
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param bool $asObject
     * @return array|object|null
     */
    public function getData($asObject = false)
    {
        $this->loadContent();
        if (!is_array($this->data) || !array_key_exists('data', $this->data)) {
            return null;
        }
        if ($asObject && !empty($this->data['data'])) {
            return $this->convertArrayToObject($this->data['data']);
        }
        return $this->data['data'];
    }

    /**
     * @return bool
     */
    public function isRangedResponse()
    {
        return $this->hasHeader('Content-Range');
    }

    /**
     * @return string|null
     */
    public function getUnit()
    {
        if (!$this->isRangedResponse()) {
            return null;
        }
        if ($this->unit === null) {
            $this->loadFromRangeHeader();
        }
        return $this->unit;
    }

    /**
     * @return int|null
     */
    public function getOffset()
    {
        if (!$this->isRangedResponse()) {
            return null;
        }
        if ($this->offset === null) {
            $this->loadFromRangeHeader();
        }
        return $this->offset;
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        if (!$this->isRangedResponse()) {
            return null;
        }
        if ($this->limit === null) {
            $this->loadFromRangeHeader();
        }
        return $this->limit;
    }

    /**
     * @return int|null
     */
    public function getCount()
    {
        if (!$this->isRangedResponse()) {
            return null;
        }
        if ($this->count === null) {
            $this->loadFromRangeHeader();
        }
        return $this->count;
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

    /**
     * @return mixed|null
     */
    public function getMessage()
    {
        $this->loadContent();
        if (!is_array($this->data) || !array_key_exists('message', $this->data)) {
            return null;
        }
        return $this->data['message'];
    }

    /**
     * @return bool
     */
    public function hasMessage()
    {
        return $this->hasContent() && !empty($this->getMessage());
    }

    /**
     * @return HttpResponseInterface
     */
    protected function getHttpResponse()
    {
        return $this->httpResponse;
    }

    /**
     * @return void
     */
    protected function loadFromRangeHeader()
    {
        if ($this->isRangedResponse()) {
            $rangeHeader = $this->getHeaders()['content-range'];
            if (is_array($rangeHeader)) {
                $rangeHeader = $rangeHeader[array_key_first($rangeHeader)];
            }
            $rangeHeader = trim($rangeHeader);
            if (preg_match('/^(\w+)\s+(\d+)\-(\d+)\/(\d+)$/', $rangeHeader, $matches) === 1) {
                $this->unit = $matches[1];
                $this->offset = $matches[2];
                $this->limit = $matches[3];
                $this->count = $matches[4];
            }
        }
    }

    /**
     * @return void
     */
    protected function loadContent()
    {
        if ($this->data === null) {
            if ($this->hasJsonHeader()) {
                $this->decodeJson();
            }
            if ($this->hasXmlHeader()) {
                $this->decodeXml();
            }
        }
    }

    /**
     * @return bool
     */
    protected function hasJsonHeader()
    {
        return $this->hasHeader('Content-Type', 'application/json');
    }

    /**
     * @return bool
     */
    protected function hasXmlHeader()
    {
        return $this->hasHeader('Content-Type', 'application/xml');
    }

    /**
     * @param string $name
     * @param string|null $value
     * @return bool
     */
    protected function hasHeader($name, $value = null)
    {
        $name = strtolower($name);
        if ($value === null) {
            return array_key_exists($name, $this->getHeaders());
        }
        return array_key_exists($name, $this->getHeaders()) && (
            (
                is_array($this->getHeaders()[$name]) &&
                in_array($value, $this->getHeaders()[$name])
            ) || (
                is_string($this->getHeaders()[$name]) &&
                $this->getHeaders()[$name] == $value
            )
        );
    }

    /**
     * @return bool
     */
    protected function decodeJson()
    {
        if (!$this->hasContent()) {
            return false;
        }
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
        if (!$this->hasContent()) {
            return false;
        }
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
     * @param array $data
     * @return object
     */
    protected function convertArrayToObject(array $data)
    {
        $obj = new \stdClass();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $obj->{$key} = $this->convertArrayToObject($value);
            } else {
                $obj->{$key} = $value;
            }
        }
        return $obj;
    }
}