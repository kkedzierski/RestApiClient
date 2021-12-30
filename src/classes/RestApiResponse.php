<?php
namespace RestApiClient\classes;

use CurlHandle;

class RestApiResponse{
    
    private int $statusCode;
    private array $body;
    private string $url;

    public function __construct(CurlHandle $ch, array $data)
    {;
        $this->prepareResponse($ch, $data);
    }

    private function prepareResponse(CurlHandle $ch, array $data){

        $responseInfoArray = curl_getinfo($ch);
        $this->setStatusCode($responseInfoArray['http_code']);
        $this->setContentBody($data);
        $this->setUrl($responseInfoArray['url']);
    }

    private function setStatusCode(int $statusCode):void{
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int{
        return $this->statusCode;
    }

    private function setContentBody(array $body):void{
        $this->body = $body;
    }

    public function getContentBody(): array{
        return $this->body;
    }

    private function setUrl(string $url){
        $this->url = $url;
    }

    public function getUrl(): string{
        return $this->url;
    }

}