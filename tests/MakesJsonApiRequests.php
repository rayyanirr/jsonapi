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

        TestResponse::macro('assertJsonApiValidationErrors', $this->assertJsonApiValidationErrors());
    }

    public function withoutJsonApiDocumentFormatting()
    {
        $this->formatJsonApiDocument = false;
    }

    public function json($method, $uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['accept'] = 'application/vnd.api+json';

        if ($this->formatJsonApiDocument) {
            $formattedData =  $this->getFormattedData($uri, $data);
        }
        return parent::json($method, $uri, $formattedData ?? $data, $headers, $options);
    }

    public function postJson($uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['content-type'] = 'application/vnd.api+json';
        return parent::postJson($uri, $data, $headers, $options);
    }

    public function patchJson($uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['content-type'] = 'application/vnd.api+json';
        return parent::patchJson($uri, $data, $headers, $options);
    }

    protected function assertJsonApiValidationErrors(): Closure
    {

        return function ($attribute) {

            /** @var TestResponse $this */

            $pointer  = Str::of($attribute)->startsWith('data')
                ? "/" . str_replace('.', '/', $attribute)
                : "/data/attributes/$attribute";

            try {
                $this->assertJsonFragment([
                    'source' => ['pointer' => $pointer]
                ]);
            } catch (ExpectationFailedException $th) {

                PHPUnit::fail("Failed to find a JSON:API validation error for key: $attribute"
                    . PHP_EOL . PHP_EOL
                    . $th->getMessage());
            }

            try {
                $this->assertJsonStructure([
                    'errors' => [
                        ['title', 'detail', 'source' => ['pointer']]
                    ]
                ]);
            } catch (ExpectationFailedException $th) {

                PHPUnit::fail("Failed to find a valid JSON:API error response"
                    . PHP_EOL . PHP_EOL
                    . $th->getMessage());
            }


            $this->assertHeader('content-type', 'application/vnd.api+json');
            $this->assertStatus(422);
        };
    }

    protected function getFormattedData($uri, array $data): array
    {
        $path = parse_url($uri)['path'];
        $type = (string) Str::of($path)->after('api/v1/')->before('/');
        $id = (string) Str::of($path)->after($type)->replace('/', '');

        return  [
            'data' => array_filter([
                'type' => $type,
                'id' => $id,
                'attributes' => $data
            ])
        ];
    }
}
