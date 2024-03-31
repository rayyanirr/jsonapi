<?php

use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api/v1',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->api(append: [
            ValidateJsonApiHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (ValidationException $e) {

            $title = $e->withMessages([]);
            $errors = [];

            foreach ($e->errors() as $field => $message) {

                $pointer = "/" . str_replace('.', '/', $field);

                $errors[] = [
                    'title' => $title->getMessage(),
                    'detail' => $message[0],
                    'source' => [
                        'pointer' => $pointer
                    ]
                ];
            }
            return response()->json([
                'errors' => $errors
            ], 422);
        });
    })->create();
