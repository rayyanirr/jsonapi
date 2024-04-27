<?php

namespace App\Exceptions\JsonApi;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException as ExceptionHttpException;

class HttpException extends ExceptionHttpException
{
    public function __construct(ExceptionHttpException $e)
    {

        parent::__construct($e->getStatusCode(), $e->getMessage());
    }

    public function render(Request $request): JsonResponse
    {

        $detail = $this->getMessage();

        if (method_exists($this, $method = "get{$this->getStatusCode()}Detail")) {
            $detail = $this->{$method}($request);
        }

        return response()->json([
            'errors' => [
                [
                    'title' => Response::$statusTexts[$this->getStatusCode()],
                    'detail' => $detail,
                    'status' => (string) $this->getStatusCode(),
                ],

            ],
        ], $this->getStatusCode());
    }

    public function get404Detail($request): string
    {

        if (str($this->getMessage())->startsWith('No query results for model')) {

            return "No records found with the id '{$request->getResourceId()}'"
                      ." in the '{$request->getResourceType()}' resource.";

        }

        return $this->getMessage();
    }
}
