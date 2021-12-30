<?php
namespace RestApiClient\classes;

use CurlHandle, Exception;

class RestApiResponse{
    
    private ?int $statusCode = null;
    private ?string $content = null;
    private ?string $body = null;
    private ?string $header = null;
    private string $url;

    public function __construct(CurlHandle $ch, string|null $response)
    {;
        $this->prepareResponse($ch, $response);
    }

    private function prepareResponse(CurlHandle $ch, string|null $response){

        if($response){
            [$header, $body] = explode("\r\n\r\n", $response, 2);
            $this->setHeader($header);
            $this->setBody($body);
        }

        $responseInfoArray = curl_getinfo($ch);
        $this->setStatusCode($responseInfoArray['http_code']);
        $this->setContent($response);
        $this->setUrl($responseInfoArray['url']);
    }

    private function setStatusCode(int $statusCode):void{
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): ?int{
        return $this->statusCode;
    }

    private function setContent(string|null $content):void{
        $this->content = $content;
    }

    public function getContent(): string|null {
        return $this->body;
    }

    private function setUrl(string $url){
        $this->url = $url;
    }

    public function getUrl(): string{
        return $this->url;
    }

    private function setBody(string|null $body){
        $this->body = $body;
    }

    public function getBody(): string|null{
        return $this->body;
    }

    private function setHeader(string|null $header){
        $this->header = $header;
    }

    public function getHeader(): string|null{
        return $this->header;
    }

    public function getHeaderLine(string $searchType):string{
        if(!$this->header){
            throw new Exception("Header is empty");
        }
        $headerArray = preg_split("/\r\n|\n|\r/", $this->getHeader());
        if(strtolower($searchType) === strtolower("http")){
            return $headerArray[0];
        }
        foreach($headerArray as $headerLine){
            $headerTypeValueArray = explode(": ", $headerLine);
            $headerType = $headerTypeValueArray[0];
            $headerValue = $headerTypeValueArray[1] ?? '';
            if(strtolower($headerType) === strtolower($searchType)){
                return $headerValue;
            }
        }
        return "Not found $searchType";

    }


}