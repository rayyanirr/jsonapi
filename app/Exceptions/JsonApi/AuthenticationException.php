<?php

namespace App\Exceptions\JsonApi;

use Exception;


class AuthenticationException extends Exception
{
    public function render()
    {
        return response()->json([
            'errors' => [
                [
                    'title' => 'Unauthenticated',
                    'detail' =>'This action required authentication.',
                    'status' => '401'
                ]

            ]
        ], 401);
    }
}
