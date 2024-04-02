<?php

namespace App\JsonApi;

use Closure;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\Assert as PHPUnit;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Str;

class JsonApiTestResponse
{


    public function assertJsonApiValidationErrors(): Closure  {

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

}
