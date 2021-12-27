<?php

include_once "RestApiHeader.php";
include_once "helpers/helpers.php";

class RestApiClient
{

    private string $apiURL;
    private string $authType;
    private CurlHandle $curlHandle;
    private ?array $header = null;

    /**
     * Initialize API URL and set curl handle
     *
     * @param string  $apiURL 
     * @param string  $authType type of authentication, default = "basic"
     * 
     * @return void
     */
    public function __construct(string $apiURL, string $authType = "basic")
    {
        if ($apiURL[-1] === '/') {
            $apiURL = substr($apiURL, 0, -1);
        }
        $this->apiURL = $apiURL;
        $this->authType = $authType;

        $this->setCurlHandle($this->apiURL);
    }

    private function setCurlHandle(string $apiUrl): void
    {
        try {
            $this->curlHandle = curl_init($apiUrl);
        } catch (Exception $e) {
            echo "Error for create Curl Handle instance: $e->getMessage()";
        }
    }

    private function getCurlHandle(): CurlHandle
    {
        if (!$this->curlHandle) {
            throw new Exception("Curl Handle instance is not created");
        }
        return $this->curlHandle;
    }

    public function getHeader(){
        return $this->header;
    }

    /**
     * Add header property type => value
     *
     * @param string  $headerType 
     * @param string  $headerValue
     * 
     * @return void
     */
    public function addToHeader(string $headerType, string $headerValue): void
    {
        if(empty($headerType) || empty($headerValue) ){
            throw new Exception("First and second parameter cannot be empty");
        }
        
        $this->header[] = "$headerType: $headerValue";
        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $this->header);

    }

    /**
     * Add Authentication token to header
     *
     * @param string  $token
     * 
     * @return void
     */
    public function setToken(string $token): void
    {
        $this->addToHeader("Authorization", "token $token");
    }

    private function getStatusCode(CurlHandle $curlHandle, ?array $data = null): string
    {
        $statusCode = curl_getinfo($curlHandle, CURLINFO_RESPONSE_CODE);
        switch ($statusCode) {
            case 200:
                return "Response with code $statusCode Success";
            case 201:
                return "Response with code $statusCode Created";
            case 404:
                return "Response with code $statusCode Page do not exists";
            case 422:
                print_r($data) ?? null;
                return "Response with code $statusCode InvalidData";
            default:
                return "Response with code $statusCode Unexpected status";
        }
    }

    private function prepareRequestURL(
        string $resource = '/',
        string $parameterValue = '',
        ?array $additionalField = null
    ): string {

        if (!$resource || $resource[0] !== "/") {
            throw new Exception("First parametr must start with /");
        }

        if ($resource[-1] === '/') {
            $resource = substr($resource, 0, -1);
        }
        if ($additionalField) {
            foreach ($additionalField as $key => $value) {
                if (strtolower($key) === 'header') {
                    if (is_array($value)) {
                        foreach ($value as $headerParameter) {
                            $headerParamArray = explode(': ', $headerParameter);
                            $this->addToHeader($headerParamArray[0], $headerParamArray[1]);
                        }
                    }
                }
            }
        }

        if (strpos($resource, ':') && $parameterValue) {
            $resource = addParametrToUrl($resource, $parameterValue);
        }

        return $resource;
    }

    private function executeRequest(string $type, string $resource, ?array $data = null): array
    {

        $type = strtoupper($type);

        $ch = $this->getCurlHandle();
        curl_setopt($ch, CURLOPT_URL, $this->apiURL . $resource);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        switch ($type) {
            case "POST":
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case "PATCH":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
                throw new Exception("Method $type is not allowed");
        }

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        echo $this->getStatusCode($ch);

        return $data;
    }

    /**
     * Send GET request
     *
     * @param string  $resource must start with "/" ex. /products or /products/:id
     * @param string  $parameterValue parametr required if is specific in resource , default = ''
     * @param array   $additionalField ex. header, default = null
     * 
     * @return array response
     */
    public function get(
        string $resource = '/',
        string $parameterValue = '',
        ?array $additionalField = null
    ): array {

        $resource = $this->prepareRequestURL($resource, $parameterValue, $additionalField);
        $response = $this->executeRequest("GET", $resource);

        return $response;
    }

    /**
     * Send POST request
     *
     * @param string  $resource must start with "/" ex. /products or /products/:id
     * @param array   $postData data to send
     * @param array   $additionalField ex. header, default = null
     * 
     * @return array response
     */
    public function post(string $resource = '/', array $postData, ?array $additionalField = null)
    {

        $resource = $this->prepareRequestURL($resource, '', $additionalField);
        $response = $this->executeRequest("POST", $resource, $postData);

        return $response;
    }

    /**
     * Send PATCH request
     *
     * @param string  $resource must start with "/" ex. /products or /products/:id
     * @param string  $parameterValue parametr required if is specific in resource , default = ''
     * @param array   $updateData data to update
     * @param array   $additionalField ex. header, default = null
     * 
     * @return array response
     */
    public function patch(
        string $resource = '/',
        string $parameterValue = '',
        array $updateData,
        ?array $additionalField = null
    ) {

        $resource = $this->prepareRequestURL($resource, $parameterValue, $additionalField);
        $response = $this->executeRequest("PATCH", $resource, $updateData);

        return $response;
    }

    /**
     * Send DELETE request
     *
     * @param string  $resource must start with "/" ex. /products or /products/:id
     * @param string  $parameterValue parametr required if is specific in resource , default = ''
     * @param array   $additionalField ex. header
     * 
     * @return array response
     */
    public function delete(
        string $resource = '/',
        string $parameterValue = '',
        ?array $additionalField = null
    ) {

        $resource = $this->prepareRequestURL($resource, $parameterValue, $additionalField);
        $response = $this->executeRequest("DELETE", $resource);

        return $response;
    }
}
