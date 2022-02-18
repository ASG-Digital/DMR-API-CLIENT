<?php

namespace ASG\DMRAPI\Basic;

use ASG\DMRAPI\Exceptions\HttpClientException;
use ASG\DMRAPI\HttpClientInterface;
use ASG\DMRAPI\HttpResponseInterface;

class CurlHttpClient implements HttpClientInterface
{
    /**
     * @return string
     */
    public function getUserAgent()
    {
        return 'CurlHttpClient cURL: (' . curl_version()['version'] . ')';
    }

    /**
     * @param string $uri
     * @param array $headers
     * @return HttpResponseInterface
     * @throws HttpClientException
     */
    public function get($uri, array $headers)
    {
        return $this->doRequest('GET', $uri, $headers);
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param string|null $data
     * @return HttpResponseInterface
     * @throws HttpClientException
     */
    public function post($uri, array $headers, $data = null)
    {
        return $this->doRequest('POST', $uri, $headers, $data);
    }

    /**
     * @param string $uri
     * @param array $headers
     * @return HttpResponseInterface
     * @throws HttpClientException
     */
    public function delete($uri, array $headers)
    {
        return $this->doRequest('DELETE', $uri, $headers);
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param string|null $data
     * @return HttpResponseInterface
     * @throws HttpClientException
     */
    public function put($uri, array $headers, $data = null)
    {
        return $this->doRequest('PUT', $uri, $headers, $data);
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param string|null $data
     * @return HttpResponseInterface
     * @throws HttpClientException
     */
    public function patch($uri, array $headers, $data = null)
    {
        return $this->doRequest('PATCH', $uri, $headers, $data);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $headers
     * @param string|null $data
     * @return Response
     * @throws HttpClientException
     */
    private function doRequest($method, $uri, array $headers, $data = null)
    {
        $responseHeaders = [];
        $builtHeaders = [];
        foreach ($headers as $name => $value) {
            $builtHeaders[] = $name . ': ' . $value;
        }
        $ch = curl_init();
        $curlOptions = [
            CURLOPT_URL => $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HEADERFUNCTION => function ($curl, $header) use (&$responseHeaders) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) {
                    return $len;
                }

                $responseHeaders[strtolower(trim($header[0]))][] = trim($header[1]);
                return $len;
            },
        ];
        if (!empty($builtHeaders)) {
            $curlOptions[CURLOPT_HTTPHEADER] = $builtHeaders;
        }
        if (!empty($data)) {
            $curlOptions[CURLOPT_POSTFIELDS] = $data;
        }
        curl_setopt_array($ch, $curlOptions);
        if (($data = curl_exec($ch)) === false) {
            $err_msg = curl_error($ch);
            $err_no = curl_errno($ch);
            curl_close($ch);
            throw new HttpClientException($err_msg, $err_no);
        }
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        return new Response($code, $responseHeaders, $data, $uri);
    }
}