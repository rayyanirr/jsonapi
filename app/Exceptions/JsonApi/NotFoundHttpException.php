<?php

namespace App\Exceptions\JsonApi;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotFoundHttpException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request)
    {

        $detail = "The route {$request->path()} could not be found.";

        if (str($this->getMessage())->startsWith('No query results for model')) {

            $type = $request->filled('data.type')
                ? $request->input('data.type')
                : (string) Str::of($request->path())->after('api/v1/')->before('/');

            $id = $request->filled('data.id')
                ? $request->input('data.id')
                : (string) Str::of($request->path())->after($type)->replace('/', '');

            if ($id && $type) {
                $detail = "No records found with the id '{$id}' in the '{$type}' resource.";
            }
        }


        return response()->json([
            'errors' => [
                [
                    'title' => 'Not Found',
                    'detail' => $detail,
                    'status' => '404',
                ],

            ],
        ], 404);
    }
}
