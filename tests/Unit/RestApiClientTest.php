<?php

namespace Tests\Unit;

use RestApiClient\classes\RestApiClient;
use PHPUnit\Framework\TestCase;
use RestApiClient\classes\RestApiResponse;

class RestApiClientTest extends TestCase
{   
    private RestApiClient $restApiClient;
    private RestApiResponse $restApiResponseInstance;

    protected function setUp():void
    {
        $apiUrl = "https://api.github.com";
        $headerArray = ["User-Agent: Test REST API Client", 
        "Authorization: token token_api"];
        $this->setRestApiClientData($apiUrl, $headerArray);

        $this->restApiResponseInstance = new RestApiResponse(curl_init("https://api.github.com"),null);
    }

    private function setRestApiClientData(string $apiURL, array $headerArray = []){
        $restApiClient = new RestApiClient($apiURL);
        if(!empty($headerArray)){
            foreach ($headerArray as $headerParameter) {
                $headerParamArray = explode(': ', $headerParameter);
                $restApiClient->addToHeader($headerParamArray[0], $headerParamArray[1]);
            }
        }
        $this->restApiClient = $restApiClient;
    }
 
    private function executeRequestsForAllMethods(
        string $method,
        string $resource,
        mixed $data,
        mixed $additonalFields = null
    ): RestApiResponse{
        switch($method){
            case 'GET':              
                $response = $this->restApiClient->get($resource, $data, $additonalFields);
                break;
            case 'POST':
                $response = $this->restApiClient->post($resource, $data, $additonalFields);
                break;
            case 'PATCH':
                $response = $this->restApiClient->patch($resource, $data, [$data], $additonalFields);
                break;
            case 'DELETE':
                $response = $this->restApiClient->delete($resource, $data, $additonalFields);
                break;
            default;
                $response = null;
        }
        return $response;
    }


    /** @test */
    public function is_add_to_header(): void{
        $RAC = new RestApiClient("https://api.github.com");
        $RAC->addToHeader("Type", "Value");
        $this->assertSame(["Type: Value"], $RAC->getHeader());
    }

    /** @test */
    public function add_to_header_method_do_not_duplicate_values_in_header(): void{
        $RAC = new RestApiClient("https://api.github.com");
        $RAC->addToHeader("Type", "Value");
        $RAC->addToHeader("Type", "Value");
        $this->assertNotSame(["Type: Value", "Type: Value"], $RAC->getHeader());
    }

    /** @test */
    public function is_get_header_line_return_correct_value(): void{
        $RAC = new RestApiClient("https://api.github.com");
        $RAC->addToHeader("Type", "Value");
        $this->assertSame("Value", $RAC->getHeaderLine("type"));
    }

    /** 
     * @test 
     * @dataProvider \Tests\DataProviders\RestApiClientDataProvider::RestApiClientMethodsParams
     */   
    public function is_request_method_return_response_object(
        string $method,
        string $resource,
        string|array $data
    ): void{
        $response = $this->executeRequestsForAllMethods($method, $resource, $data);
        $this->assertInstanceOf(get_class($this->restApiResponseInstance), $response);
    }

    /** 
     * @test 
     * @dataProvider \Tests\DataProviders\RestApiClientDataProvider::RestApiClientMethodsParams
    */   
    public function is_request_return_response_object_with_status_code(
        string $method,
        string $resource,
        string|array $data
    ): void{
        $response = $this->executeRequestsForAllMethods($method, $resource, $data);
        $this->assertIsInt($response->getStatusCode());
    }

    /** 
     * @test 
     * @dataProvider \Tests\DataProviders\RestApiClientDataProvider::RestApiClientMethodsParams
    */  
    public function is_request_return_response_object_with_body(
        string $method,
        string $resource,
        string|array $data
    ): void{
        $response = $this->executeRequestsForAllMethods($method, $resource, $data);
        $this->assertNotNull($response->getBody());
    }

    /** 
     * @test 
     * @dataProvider \Tests\DataProviders\RestApiClientDataProvider::RestApiClientMethodsParams
    */ 
    public function is_request_return_response_object_with_header(
        string $method,
        string $resource,
        string|array $data
    ): void{
        $response = $this->executeRequestsForAllMethods($method, $resource, $data);
        $this->assertNotNull($response->getHeader());
    }

    /** 
     * @test 
     * @dataProvider \Tests\DataProviders\RestApiClientDataProvider::RestApiClientMethodsParams
    */ 
    public function is_request_return_response_object_with_URL(
        string $method,
        string $resource,
        string|array $data
    ): void{
        $response = $this->executeRequestsForAllMethods($method, $resource, $data);
        $this->assertNotNull($response->getUrl());
    }

    /** 
     * @test 
     * @dataProvider \Tests\DataProviders\RestApiClientDataProvider::RestApiClientMethodsInvalidParams
    */ 
    public function is_throw_first_charater_is_not_backslash_exception_in_resource_argument(
        string $method,
        string $resource,
        string|array $data
    ): void{
        $this->expectException(\InvalidArgumentException::class);
        $this->executeRequestsForAllMethods($method, $resource, $data);
    }

    /** 
     * @test 
     * @dataProvider \Tests\DataProviders\RestApiClientDataProvider::RestApiClientMethodsParams
    */ 
    public function is_remove_last_backslash_from_resource(
        string $method,
        string $resource,
        string|array $data
    ): void{
        $response = $this->executeRequestsForAllMethods($method, $resource, $data);
        $this->assertNotSame('/', $response->getUrl()[-1]);
    }

    /** 
     * @test 
     * @dataProvider \Tests\DataProviders\RestApiClientDataProvider::RestApiClientMethodsParamsWithParameterValue
    */ 
    public function is_add_to_resource_parametr_value(
        string $method,
        string $resource,
        string|int $parameterValue
    ): void{
        $this->executeRequestsForAllMethods($method, $resource, $parameterValue);
        $this->assertSame('https://api.github.com/user/123/repos', $this->restApiClient->getUrl());
    }

    /** 
     * @test 
     * @dataProvider \Tests\DataProviders\RestApiClientDataProvider::RestApiClientMethodsParamsWithAdditionalFieldHeader
    */ 
    public function is_add_to_header_from_additional_fields_argument_array(
        string $method,
        string $resource,
        string|array $data,
        array $additonalFields
    ): void{
        $this->executeRequestsForAllMethods($method, $resource, $data, $additonalFields);
        $this->assertSame('Value', $this->restApiClient->getHeaderLine('type'));
    }

}