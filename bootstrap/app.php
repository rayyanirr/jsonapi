<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Validation\ValidationException;
use App\Http\Middleware\ValidateJsonApiHeaders;
use App\Http\Middleware\ValidateJsonApiDocument;
use App\Exceptions\JsonApi\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Middleware\RedirectUsersIfAutenticatedMiddleware;
use App\Exceptions\JsonApi\HttpException as JsonApiHttpException;
use Illuminate\Auth\AuthenticationException as AuthAuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->name('api.v1.')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'guest' => RedirectUsersIfAutenticatedMiddleware::class,
        ]);

        $middleware->api(append: [
            ValidateJsonApiHeaders::class,
            ValidateJsonApiDocument::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->renderable(function (HttpException $e, Request $request) {

            $request->isJsonApi() && throw new JsonApiHttpException($e);
        });

        $exceptions->renderable(function (AuthAuthenticationException $e, Request $request) {

            $request->isJsonApi() && throw new AuthenticationException();
        });

        $exceptions->render(function (ValidationException $e, Request $request) {

            if ($request->isJsonApi()) {
                return new JsonApiValidationErrorResponse($e);
            }

            return false;
        });
    })->create();
