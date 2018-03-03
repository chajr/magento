<?php

class Chajr_FreshSales_Helper_Curl
{
    /**
     * @var resource
     */
    protected $curl;

    /**
     * @var array
     */
    protected $curlOptions = [
        'connection_timeout' => 10,
        'timeout' => 10,
        'post' => 0,
        'return_transfer' => 1,
    ];

    public function __construct()
    {
        $this->curl = curl_init();
    }

    /**
     * @param string $uri
     * @param string $content
     * @param array $headers
     * @param array $options
     * @return array
     */
    public function post($uri, $content, array $headers = [], array $options = [])
    {
        $this->curlOptions['post'] = 1;
        return $this->makeRequest($uri, $content, $headers, $options);
    }

    /**
     * @param string $uri
     * @param string $content
     * @param array $headers
     * @param array $options
     * @return array
     */
    public function makeRequest($uri, $content, array $headers = [], array $options = [])
    {
        $this->curlOptions = array_merge($this->curlOptions, $options);

        curl_setopt($this->curl, CURLOPT_URL, $uri);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $content);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);

        return $this->setCurlOptions()->handleCurlResponse();
    }

    /**
     * @return $this
     */
    protected function setCurlOptions()
    {
        curl_setopt($this->curl, CURLOPT_POST, $this->curlOptions['post']);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, $this->curlOptions['return_transfer']);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->curlOptions['connection_timeout']);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->curlOptions['timeout']);
        curl_setopt($this->curl, CURLOPT_VERBOSE, 1);

        return $this;
    }

    /**
     * @return array
     */
    protected function handleCurlResponse()
    {
        $response = curl_exec($this->curl);
        $httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $errors = curl_error($this->curl);

        return [
            'code' => $httpCode,
            'error' => $errors,
            'response' => $response
        ];
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }
}
