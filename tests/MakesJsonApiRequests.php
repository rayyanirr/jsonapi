<?php

namespace tests;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\Assert as PHPUnit;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Str;
use Closure;

trait MakesJsonApiRequests
{

    protected bool $formatJsonApiDocument = true;

    protected  function setUp(): void
    {
        parent::setUp();

        TestResponse::macro('assertJsonApiValidationErrors', $this->assertJsonApiValidationErrors() );
    }

    public function withoutJsonApiDocumentFormatting() {
        $this->formatJsonApiDocument = false;
    }

    public function json($method, $uri, array $data = [], array $headers = [], $options = 0): \Illuminate\Testing\TestResponse
    {
        $headers['accept'] = 'application/vnd.api+json';

        if ($this->formatJsonApiDocument){

            $formattedData['data']['attributes'] = $data;

            $formattedData['data']['type'] = (string) Str::of($uri)->after('api/v1/');
        }



        return parent::json($method, $uri, $formattedData ?? $data, $headers, $options);
    }

    public function postJson($uri, array $data = [], array $headers = [], $options = 0): \Illuminate\Testing\TestResponse
    {
        $headers['content-type'] = 'application/vnd.api+json';
        return parent::postJson($uri, $data, $headers, $options);
    }

    public function patchJson($uri, array $data = [], array $headers = [], $options = 0): \Illuminate\Testing\TestResponse
    {
        $headers['content-type'] = 'application/vnd.api+json';
        return parent::patchJson($uri, $data, $headers, $options);
    }

    protected function assertJsonApiValidationErrors() : Closure {

        return function ($attribute) {

            /** @var TestResponse $this */

            $pointer  = Str::of($attribute)->startsWith('data')
                            ? "/".str_replace('.','/',$attribute)
                            : "/data/attributes/$attribute";

            try {
                $this->assertJsonFragment([
                    'source' => ['pointer' => $pointer]
                ]);
            } catch (ExpectationFailedException $th) {

                PHPUnit::fail("Failed to find a JSON:API validation error for key: $attribute"
                .PHP_EOL.PHP_EOL
                .$th->getMessage() );
            }

            try {
                $this->assertJsonStructure([
                    'errors' => [
                        ['title', 'detail', 'source' => ['pointer']]
                    ]
                    ]);
            }catch (ExpectationFailedException $th) {

                PHPUnit::fail("Failed to find a valid JSON:API error response"
                .PHP_EOL.PHP_EOL
                .$th->getMessage() );
            }


            $this->assertHeader('content-type', 'application/vnd.api+json');
            $this->assertStatus(422);
        };
    }
}
