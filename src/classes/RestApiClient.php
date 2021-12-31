<?php
namespace RestApiClient\Classes;
use RestApiClient\Classes\Helpers\Helpers;


use \InvalidArgumentException, \Exception, \CurlHandle;
use RestApiClient\classes\RestApiRequest;
use RestApiClient\classes\RestApiResponse;

class RestApiClient
{

    private string $baseURI;
    private CurlHandle $curlHandle;
    private array $header = [];
    private string $url;

    /**
     * Initialize base URI and curl handle
     *
     * @param string  $base URI 
     * 
     * @return void
     */
    public function __construct(string $baseURI)
    {
        if ($baseURI[-1] === '/') {
            $baseURI = substr($baseURI, 0, -1);
        }
        $this->baseURI = $baseURI;

        $this->setCurlHandle($this->baseURI);
    }

    private function setCurlHandle(string $baseURI): void
    {
        try {
            $this->curlHandle = curl_init($baseURI);
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

    private function setUrl(string $url){
        $this->url = $url;
    }

    public function getUrl(): string{
        return $this->url;
    }

    public function getHeader(){
        return $this->header;
    }

    public function getHeaderLine(string $searchType): string{
        if(!$this->header){
            throw new Exception("Header is empty");
        }
        foreach($this->header as $headerLine){
            $headerTypeValueArray = explode(": ", $headerLine);
            $headerType = $headerTypeValueArray[0];
            
            $headerValue = $headerTypeValueArray[1] ?? '';
            if(strtolower($headerType) === strtolower($searchType)){
                return $headerValue;
            }
        }

        return "Not found $searchType";
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
        if(!in_array("$headerType: $headerValue", $this->header)){
            $this->header[] = "$headerType: $headerValue";
        }
        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $this->header);

    }

    private function prepareRequestURL(
        string $resource = '/',
        string|int $parameterValue = '',
        ?array $additionalField = null
    ): string {

        if (!$resource || $resource[0] !== "/") {
            throw new InvalidArgumentException("First parametr must start with /");
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
            $resource = Helpers::addParametrToUrl($resource, $parameterValue);
        }

        return $resource;
    }

    private function executeRequest(string $type, string $resource, ?array $data = null): RestApiResponse
    {

        $type = strtoupper($type);

        $ch = $this->getCurlHandle();
        $this->setUrl($this->baseURI . $resource);
        
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        switch ($type) {
            case "GET":
                break;
            case "POST":
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case "PATCH":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case "PUT":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
                throw new InvalidArgumentException("Method $type is not allowed");
        }

        $response = curl_exec($ch);
        curl_close($ch);

        $responseObj = new RestApiResponse($ch, $response);
        
        return $responseObj;
    }


    /**
     * Send GET request
     *
     * @param string  $resource must start with "/" ex. /products or /products/:id
     * @param string  $parameterValue parametr required if is specific in resource , default = ''
     * @param array   $additionalField ex. header, default = null
     * 
     * @return RestApiResponse response
     */
    public function get(
        string $resource = '/',
        string|int $parameterValue = '',
        ?array $additionalField = null
    ): RestApiResponse {

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
     * @return RestApiResponse response
     */
    public function post(
        string $resource = '/', 
        array $postData,
        ?array $additionalField = null
    ): RestApiResponse
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
     * @return RestApiResponse response
     */
    public function patch(
        string $resource = '/',
        string|int $parameterValue = '',
        array $updateData,
        ?array $additionalField = null
    ): RestApiResponse {

        $resource = $this->prepareRequestURL($resource, $parameterValue, $additionalField);
        $response = $this->executeRequest("PATCH", $resource, $updateData);

        return $response;
    }

    /**
     * Send PUT request
     *
     * @param string  $resource must start with "/" ex. /products or /products/:id
     * @param string  $parameterValue parametr required if is specific in resource , default = ''
     * @param array   $updateData data to update
     * @param array   $additionalField ex. header, default = null
     * 
     * @return RestApiResponse response
     */
    public function put(
        string $resource = '/',
        string|int $parameterValue = '',
        array $updateData,
        ?array $additionalField = null
    ): RestApiResponse {

        $resource = $this->prepareRequestURL($resource, $parameterValue, $additionalField);
        $response = $this->executeRequest("PUT", $resource, $updateData);

        return $response;
    }

    /**
     * Send DELETE request
     *
     * @param string  $resource must start with "/" ex. /products or /products/:id
     * @param string  $parameterValue parametr required if is specific in resource , default = ''
     * @param array   $additionalField ex. header
     * 
     * @return RestApiResponse response
     */
    public function delete(
        string $resource = '/',
        string|int $parameterValue = '',
        ?array $additionalField = null
    ): RestApiResponse {

        $resource = $this->prepareRequestURL($resource, $parameterValue, $additionalField);
        $response = $this->executeRequest("DELETE", $resource);

        return $response;
    }
}