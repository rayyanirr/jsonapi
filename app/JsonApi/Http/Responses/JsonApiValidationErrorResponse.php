<?php

namespace App\JsonApi\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class JsonApiValidationErrorResponse extends JsonResponse
{
    public function __construct(\Illuminate\Validation\ValidationException $e, $status = 422)
    {
        $data = $this->formatJsonApiError($e);
        $headers = [
            'content-type' => 'application/vnd.api+json',
        ];
        parent::__construct($data, $status, $headers);
    }

    protected function formatJsonApiError(ValidationException $e): array
    {

        $title = $e->withMessages([]);


        return [
            'errors' => collect($e->errors())
                ->map(function ($message, $field) use ($title) {
                    return [
                        'title' => $title->getMessage(),
                        'detail' => $message[0],
                        'source' => [
                            'pointer' => '/'.str_replace('.', '/', $field),
                        ],
                    ];
                })->values(),
        ];
    }
}
