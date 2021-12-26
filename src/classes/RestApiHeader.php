<?php

class RestApiHeader{

    private const HEADER_ACCEPT_TYPES = [
        0 => 'Authorization',
        1 => 'User-Agent',
        2 => 'Cache-Control',
        3 => 'Content-ID',
        4 => 'Content-Length',
        5 => 'Content-Range',
        6 => 'Content-Type',
        7 => 'Content-Transfer-Encoding',
        8 => 'Date',
        9 => 'ETag',
        10 => 'Expires',
        11 => 'Host',
        12 => 'If-Match',
        13 => 'If-None-Match',
        14 => 'Location',
        15 => 'Range'
    ];
    
    private array $header = [];

    public function getHeader(){
        return $this->header;
    }

    /**
     * Add header property type => value
     *
     * @param string  $headerType 
     * @param string  $headerValue
     * 
     * @return RestApiHeader
     */ 
    public function addProperty(string $headerType, string $headerValue): RestApiHeader {
        if(!in_array($headerType, SELF::HEADER_ACCEPT_TYPES)){
            throw new Exception("Invalid header Type: \"$headerType\", 
                instead of this choose: ". implode(', ', SELF::HEADER_ACCEPT_TYPES));
        }
        if($headerValue === ''){
            throw new Exception("Secound parameter cannot be empty");
        }
        
        $this->header[] = "$headerType: $headerValue";
        
        return $this;
        
    }


}