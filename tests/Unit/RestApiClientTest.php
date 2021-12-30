<?php

namespace Tests\Unit;

use RestApiClient\classes\RestApiClient;
use PHPUnit\Framework\TestCase;

class RestApiClientTest extends TestCase
{   
    private RestApiClient $restApiClient;

    protected function setUp():void
    {
        $apiUrl = "https://api.github.com";
        $headerArray = ["User-Agent: Test REST API Client", 
        "Authorization: token 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"];
        $this->setRestApiClientData($apiUrl, $headerArray);
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

    private function getRestApiClient(): RestApiClient{
 
        return $this->restApiClient;
    }

    /** @test */
    public function is_add_to_header(): void{
        $RAC = new RestApiClient("https://api.github.com");
        $RAC->addToHeader("Type", "Value");
        $this->assertEquals(["Type: Value"], $RAC->getHeader());
    }

    /** @test */   
    public function is_request_return_status_code(): void{
        $RAC = $this->getRestApiClient();
        $response = $RAC->get('/user/repos');

        $this->assertIsInt($response->getStatusCode());
    }

    /** @test */
    public function is_get_request_return_body_content(): void{
        $RAC = $this->getRestApiClient();
        $response = $RAC->get('/user/repos');
        $this->assertNotNull($response->getContent());
    }

    /** @test */
    public function is_get_request_return_response_with_header(): void{
        $RAC = $this->getRestApiClient();
        $response = $RAC->get('/user/repos');
        $this->assertNotNull($response->getHeader());
    }

    /** @test */
    public function is_get_request_return_response_with_URL(): void{
        $RAC = $this->getRestApiClient();
        $response = $RAC->get('/user/repos');
        $this->assertNotNull($response->getUrl());
    }
}