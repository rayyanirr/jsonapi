<?php

use App\Http\Controllers\Auth\AutheticatedUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Kfc\CuponController;
use App\JsonApi\Middleware\ValidateJsonApiDocument;
use App\JsonApi\Middleware\ValidateJsonApiHeaders;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware([ValidateJsonApiDocument::class, ValidateJsonApiHeaders::class])
    ->group(function () {
        Route::post('login', LoginController::class)
            ->name('login');

        Route::post('logout', LogoutController::class)
            ->name('logout');

        Route::post('register', RegisterController::class)
            ->name('register');

        Route::get('user', AutheticatedUserController::class)->middleware('auth:sanctum')->name('auth.user');
    });

Route::apiResource('cupons', CuponController::class)->middleware('auth:sanctum');
