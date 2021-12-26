<?php

include_once "RestApiHeader.php";
include_once "helpers/helpers.php";

class RestApiClient{

    private string $apiURL;
    private string $authType;
    private CurlHandle $curlHandle;
    private ?RestApiHeader $header = null;

    /**
     * Initialize API URL and set curl handle
     *
     * @param string  $apiURL 
     * @param string  $authType type of authentication, default = "basic"
     * 
     * @return void
     */ 
    public function __construct(string $apiURL, string $authType="basic"){
        if($apiURL[-1] === '/'){
            $apiURL = substr($apiURL, 0, -1);
        }
        $this->apiURL = $apiURL;
        $this->authType = $authType;

        $this->setCurlHandle($this->apiURL);
    }

    private function setCurlHandle(string $apiUrl): void{
        try{
            $this->curlHandle = curl_init($apiUrl);
        }catch(Exception $e){
            echo "Error for create Curl Handle instance: $e->getMessage()";
        }
       
    }

    private function getCurlHandle(): CurlHandle{
        if(!$this->curlHandle){
            throw new Exception("Curl Handle instance is not created");
        }
        return $this->curlHandle;
    }

    private function setHeader(){
        $this->header = new RestApiHeader();
    }

    private function getHeader(): RestApiHeader|null {
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
    public function addToHeader(string $headerType, string $headerValue): void{
        if(!$this->curlHandle){
            throw new Exception("Curl Handle instance is not created");
        }
        if(!$this->getHeader()){
            $this->setHeader();
        }
        $headerObj = $this->getHeader();
        $header = $headerObj
                        ->addProperty($headerType, $headerValue)
                        ->getHeader();  
        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $header);
    }

    /**
     * Add Authentication token to header
     *
     * @param string  $token
     * 
     * @return void
     */ 
    public function setToken(string $token): void{
        $this->addToHeader("Authorization", "token $token");
    }

    public function get(string $resource = '/', string $parameterValue = '', ?array $additionalField = null): string{
    
        if(!$resource || $resource[0] !== "/"){
            throw new Exception("First parametr must start with /");
        }

        if($resource[-1] === '/'){
            $resource = substr($resource, 0, -1);
        }

        if(strpos($resource, ':') && $parameterValue){
            $resource = addParametrToUrl($resource, $parameterValue);
        }
        
        $ch = $this->getCurlHandle();
        curl_setopt($ch, CURLOPT_URL, $this->apiURL . $resource);

        if($additionalField){
            foreach($additionalField as $key => $value){
                if(strtolower($key) === 'header'){
                    if(is_array($value)){
                        foreach($value as $headerParameter){
                            $headerParamArray = explode(': ', $headerParameter);
                            $this->addToHeader($headerParamArray[0], $headerParamArray[1]);
                        }
                    }
                }
            }
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
        
    }
}