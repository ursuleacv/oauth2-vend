<?php

namespace League\OAuth2\Client\Provider\VendAPI;

class VendRequest
{
    /**
     * @var mixed
     */
    private $curl;
    /**
     * @var mixed
     */
    private $curl_debug;
    /**
     * @var mixed
     */
    private $debug;
    /**
     * @var mixed
     */
    private $cookie;
    /**
     * @var mixed
     */
    private $http_header;
    /**
     * @var mixed
     */
    private $http_body;

    /**
     * @var mixed
     */
    public $http_code;

    /**
     * @param $url
     * @param $username
     * @param $password
     */
    public function __construct($url, $username, $password)
    {
        $this->curl = curl_init();

        $this->url = $url;

        // setup default curl options
        $options = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_FAILONERROR => 0, // 0 allows us to process the 400 responses (e.g. rate limits)
            CURLOPT_HTTPAUTH => CURLAUTH_ANY,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: ' . $username . ' ' . $password,
            ),
            CURLOPT_HEADER => 1,
        );

        $this->setOpt($options);
    }

    public function __destruct()
    {
        // close curl nicely
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
    }

    /**
     * set option for request, also accepts an array of key/value pairs for the first param
     * @param string $name  option name to set
     * @param misc $value value
     */
    public function setOpt($name, $value = false)
    {
        if (is_array($name)) {
            curl_setopt_array($this->curl, $name);
            return;
        }
        if ($name == 'debug') {
            curl_setopt($this->curl, CURLINFO_HEADER_OUT, (int) $value);
            curl_setopt($this->curl, CURLOPT_VERBOSE, (boolean) $value);
            $this->debug = $value;
        } else {
            curl_setopt($this->curl, $name, $value);
        }
    }

    /**
     * @param $path
     * @param $rawdata
     * @return mixed
     */
    public function post($path, $rawdata)
    {
        $this->setOpt(
            array(
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $rawdata,
                CURLOPT_CUSTOMREQUEST => 'POST',
            )
        );
        $this->posted = $rawdata;
        return $this->request($path, 'post');
    }

    /**
     * @param $path
     * @return mixed
     */
    public function get($path)
    {
        $this->setOpt(
            array(
                CURLOPT_HTTPGET => 1,
                CURLOPT_POSTFIELDS => null,
                CURLOPT_CUSTOMREQUEST => 'GET',
            )
        );
        $this->posted = '';
        return $this->request($path, 'get');
    }

    /**
     * @param $path
     * @param $type
     * @return mixed
     */
    private function request($path, $type)
    {
        $this->setOpt(CURLOPT_URL, $this->url . $path);

        $this->response = $response = curl_exec($this->curl);
        $curl_status = curl_getinfo($this->curl);
        $this->http_code = $curl_status['http_code'];
        $header_size = $curl_status['header_size'];

        $this->http_header = substr($response, 0, $header_size);
        $this->http_body = substr($response, $header_size);

        if ($this->debug) {
            $this->curl_debug = $curl_status;
            $head = $foot = "\n";
            if (php_sapi_name() !== 'cli') {
                $head = '<pre>';
                $foot = '</pre>';
            }
            echo $head . $this->curl_debug['request_header'] . $foot .
            ($this->posted ? $head . $this->posted . $foot : '') .
            $head . $this->http_header . $foot .
            $head . $this->http_body . $foot;
        }
        return $this->http_body;
    }
}
