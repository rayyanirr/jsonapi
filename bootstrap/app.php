<?php

use App\Exceptions\JsonApi\AuthenticationException;
use App\Exceptions\JsonApi\BadRequestHttpException as JsonApiBadRequestHttpException;
use App\Exceptions\JsonApi\NotFoundHttpException as JsonApiNotFoundHttpException;
use App\Http\Middleware\RedirectUsersIfAutenticatedMiddleware;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;
use App\Http\Middleware\ValidateJsonApiDocument;
use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Auth\AuthenticationException as AuthAuthenticationException;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
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
            'guest' => RedirectUsersIfAutenticatedMiddleware::class
        ]);


        $middleware->api(append: [
            ValidateJsonApiHeaders::class,
            ValidateJsonApiDocument::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->renderable(function (NotFoundHttpException $e) {

           throw new JsonApiNotFoundHttpException();
        });

        $exceptions->renderable(function (BadRequestHttpException $e) {

           throw new JsonApiBadRequestHttpException($e->getMessage());
        });


        $exceptions->renderable(function (AuthAuthenticationException $e) {

           throw new AuthenticationException();
        });


        $exceptions->render(function (ValidationException $e, Request $request) {

            if ( ! $request->routeIs('api.v1.login') ) {
                return new JsonApiValidationErrorResponse($e);
            }

            return false;

        });
    })->create();
