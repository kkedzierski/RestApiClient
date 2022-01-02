# Rest Api Client
## library to send HTTP requests and retrieve response object 

### Instalation

***

To use library in another project write in console:  
```composer install```\
```composer require kkedzierski/rest-api-client```

### Example usage
```
<?php

use RestApiClient\RestApiClient;

require_once __DIR__ . './vendor/autoload.php';

$RAC = new RestApiClient("https://api.github.com");
$additonalParamArr = [
    "header" => 
        ["User-Agent: Test REST API Client", 
        "Authorization: token api_token"]
];
$response = $RAC->get('/user/repos', additionalField: $additonalParamArr);

// or
// $rac->addToHeader("User-Agent", "Test REST API Client");
// $rac->addToHeader("Authorization", "token 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");
// $response = $RAC->get('/user/repos');

$response->getUrl();
$response->getStatusCode();
$response->getHeader();
$response->getHeaderLine("user-agent");
$response->getBody();
$response->getContent();

```
***


