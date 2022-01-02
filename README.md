# Rest Api Client
## library to send HTTP requests and retrieve response object 

### Instalation

***

To use library in another project write in console:  
```composer install```\
```composer require kkedzierski/rest-api-client```

***

### Usage

#### After instalation composer requirements to use library at the top of file write
>use RestApiClient\RestApiClient;
>require_once __DIR__ . './vendor/autoload.php';

#### create instance of class with base URI
ex.
> $RAC = new RestApiClient("https://api.github.com");

#### To add to header use addToHeader methods, 
#### methods take two parameters Type and Value
ex.
> $rac->addToHeader("User-Agent", "Test REST API Client");

#### To add additonalParamArr like gets or header too you can create array
ex. array:
```
$additonalParamArr = [
    "header" => 
        ["User-Agent: Test REST API Client", 
        "Authorization: token api_token"]
];
```
#### To make HTTP Request use methods get, post, put, patch, delete
ex. 
> $response = $RAC->get('/user/repos');

#### To specify endpoint to GET PUT PATCH or DELETE method you can use parametr value argument
ex.
> $response = $RAC->get('/user/:id/repos', 123);

#### GET method parameters
* string  $resource must start with "/" ex. /products or /products/:id
* string|int  $parameterValue parametr required if is specific in resource , default = ''
* array   $additionalField ex. header, default = null

#### POST method parameters
 * string       $resource must start with "/" ex. /products or /products/:id
 * array        $postData data to send
 * array|null   $additionalField ex. header, default = null
 
#### PUT and PATCH methods parameters
 * string       $resource must start with "/" ex. /products or /products/:id
 * string|int       $parameterValue parametr required if is specific in resource , default = ''
 * array        $updateData data to update
 * array|null   $additionalField ex. header, default = null

#### DELETE method parameters
 * string  $resource must start with "/" ex. /products or /products/:id
 * string|int  $parameterValue parametr required if is specific in resource , default = ''
 * array   $additionalField ex. header

#### every methods return response object with methods
* getUrl();
* getStatusCode();
* getHeader();
* getHeaderLine(string);
* getBody();
* getContent();  


#### Rest Api Clinet object have additional methods:
* getUrl();
* getHeader();
* getHeaderLine(string);

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
// $rac->addToHeader("Authorization", "token api_token");
// $response = $RAC->get('/user/repos');

$response->getUrl();
$response->getStatusCode();
$response->getHeader();
$response->getHeaderLine("user-agent");
$response->getBody();
$response->getContent();

```
***


