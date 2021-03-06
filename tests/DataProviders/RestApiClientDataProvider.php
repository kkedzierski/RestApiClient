<?php

namespace Tests\DataProviders;

class RestApiClientDataProvider{

    public function RestApiClientMethodsParams(): array
    {
        return [
            ['GET', '/user/repos/', 'test'],
            ['POST', '/user/repos/', ['test']],
            ['PATCH', '/user/repos/', 'test'],
            ['PUT', '/user/repos/', 'test'],
            ['DELETE', '/user/repos/', 'test']

        ];
    }

    public function RestApiClientMethodsParamsWithParameterValue(): array
    {
        return [
            ['GET', '/user/:id/repos/', 123],
            ['PATCH', '/user/:id/repos/', 123],
            ['PUT', '/user/:id/repos/', 123],
            ['DELETE', '/user/:id/repos/', 123]
        ];
    }

    public function RestApiClientMethodsParamsWithAdditionalFieldHeader(): array
    {
        return [
            ['GET', '/user/repos/', 'test', ["header" => ["Type: Value"] ] ],
            ['POST', '/user/repos/', ['test'], ['header' => ["Type: Value"] ] ] ,
            ['PATCH', '/user/repos/', 'test', ['header' => ["Type: Value"] ] ] ,
            ['PUT', '/user/repos/', 'test', ['header' => ["Type: Value"] ] ] ,
            ['DELETE', '/user/repos/', 'test', ['header' => ["Type: Value"] ] ] 
        ];
    }

    public function RestApiClientMethodsParamsWithAdditionalFieldFields(): array
    {
        return [
            ['GET', '/user/repos/', 'test', ["fields" => ["Field = TestValue", "Field2 = TestValue2"] ] ],
            ['POST', '/user/repos/', ['test'], ['fields' => ["Field = TestValue", "Field2 = TestValue2"] ] ] ,
            ['PATCH', '/user/repos/', 'test', ['fields' => ["Field = TestValue", "Field2 = TestValue2"] ] ] ,
            ['PUT', '/user/repos/', 'test', ['fields' => ["Field = TestValue", "Field2 = TestValue2"] ] ] ,
            ['DELETE', '/user/repos/', 'test', ['fields' => ["Field = TestValue", "Field2 = TestValue2"] ] ] 
        ];
    }

    public function RestApiClientMethodsInvalidParams(): array
    {
        return [
            ['GET', 'user/repos', 'test'],
            ['POST', 'user/repos', ['test']],
            ['PATCH', 'user/repos', 'test'],
            ['PUT', 'user/repos', 'test'],
            ['DELETE', 'user/repos', 'test']

        ];
    }
}