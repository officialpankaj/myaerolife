<?php

use PHPMailer\PHPMailer;

class BaseController
{
    /**
     * __call magic method.
     */
    public function __call($name, $arguments)
    {
        $this->sendOutput('', array('HTTP/1.1 404 Not Found'));
    }

    /**
     * Get URI elements.
     *
     * @return array
     */
    protected function getUriSegments()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode('/', $uri);

        return $uri;
    }

    /**
     * Get querystring params.
     *
     * @return array
     */
    protected function getQueryStringParams($query)
    {
        return parse_str($_SERVER['QUERY_STRING'], $query);
    }

    /**
     * Send API output.
     *
     * @param mixed  $data
     * @param string $httpHeader
     */
    protected function sendOutput($data, $httpHeaders = array())
    {
        header_remove('Set-Cookie');

        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }

        $http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "";
        if (ENV == "DEV") {
            header("Access-Control-Allow-Origin: *");
        } else if (ENV == "PROD" && ($http_origin == "https://api.myaerolife.com" || $http_origin == "https://dev.myaerolife.com" || $http_origin == "https://www.myaerolife.com")) {
            header("Access-Control-Allow-Origin: $http_origin");
        }
        header("Access-Control-Expose-Headers: Content-Length, X-JSON");
        header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
        header("Access-Control-Max-Age: 86400");
        header("Content-Type: application/json");
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header("HTTP/1.1 200 Ok");
        }


        echo json_encode($data);
        exit;
    }

    protected function setUpCurl($endpoint, $requestMethod = "GET", $data = null, $header_list = null)
    {
        $curl = curl_init();

        $headers = array(
            "Content-Type: application/json",
        );

        $headers = (!empty($header_list) && $header_list !== null) ? array_merge($headers, $header_list) : $headers;

        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $requestMethod,
            CURLOPT_POSTFIELDS => ($data != null) ? json_encode($data) : '',
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_VERBOSE => ENV === 'DEV' ? true : false,
            CURLOPT_SSL_VERIFYPEER => ENV === 'PROD' ? true : false,
            CURLOPT_SSL_VERIFYHOST => ENV === 'PROD' ? true : false,
        ));

        $response = curl_exec($curl);

        if (ENV === 'DEV' && curl_errno($curl)) {
            echo 'cURL error: ' . curl_error($curl);
        }

        curl_close($curl);

        return json_decode($response);
    }
}
