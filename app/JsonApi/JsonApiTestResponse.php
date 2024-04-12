<?php

namespace App\JsonApi;

use Closure;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\Assert as PHPUnit;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Str;

class JsonApiTestResponse
{


    public function assertJsonApiValidationErrors(): Closure
    {

        return function ($attribute) {

            /** @var TestResponse $this */

            $pointer = "/data/attributes/$attribute";

            if ( Str::of($attribute)->startsWith('data')){

                $pointer =  "/" . str_replace('.', '/', $attribute);

            } else if (Str::of($attribute)->startsWith('relationships')) {

                $pointer =  "/data/" . str_replace('.', '/', $attribute).'/data/id';
            }

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
            return $this->assertStatus(422);
        };
    }

    public function assertJsonApiResource() : Closure
    {

        return function ($model, $attributes)  {
            /** @var TestResponse $this */
            return $this->assertJson([
                'data' => [
                    'type' => $model->getResourceType(),
                    'id' => (string)$model->getRouteKey(),
                    'attributes' => $attributes,
                    'links' => [
                        'self' => route('api.v1.'. $model->getResourceType() .'.show', $model),

                    ]
                ]

            ])->assertHeader(
                'Location',
                route('api.v1.'.$model->getResourceType().'.show', $model)
            );
        };
    }

    public function assertJsonApiResourceCollection() : Closure
    {

        return function ($models, $attributesKeys) {
            /** @var TestResponse $this */

            foreach ($models as $model) {

                $this->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'attributes' => $attributesKeys
                        ]
                    ]
                ]);

                $this->assertJsonFragment([
                    'type' => $model->getResourceType(),
                    'id' => (string) $model->getRouteKey(),
                    'links' => [
                        'self' => route('api.v1.'.$model->getResourceType().'.show', $model),

                    ]
                ]);
            }

            return $this;

        };
    }

}
