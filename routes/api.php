<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CommentController;
use App\JsonApi\Middleware\ValidateJsonApiHeaders;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RegisterController;
use App\JsonApi\Middleware\ValidateJsonApiDocument;
use App\Http\Controllers\Api\ArticleAuthorController;
use App\Http\Controllers\Api\CommentAuthorController;
use App\Http\Controllers\Api\CommentArticleController;
use App\Http\Controllers\Api\ArticleCategoryController;
use App\Http\Controllers\Api\ArticleCommentsController;

Route::apiResource('articles', ArticleController::class);
Route::apiResource('comments', CommentController::class);
Route::apiResource('authors', AuthorController::class)->only('index', 'show');
Route::apiResource('categories', CategoryController::class)->only('index', 'show');

Route::prefix('comments/{comment}')->group(function () {

    Route::controller(CommentArticleController::class)
        ->group(function () {
            Route::get('article', 'show')->name('comments.article');
            Route::get('relationships/article', 'index')->name('comments.relationships.article');
            Route::patch('relationships/article', 'update')->name('comments.relationships.article.update');
        });

    Route::controller(CommentAuthorController::class)
        ->group(function () {

            Route::get('relationships/author', 'index')->name('comments.relationships.author');
            Route::get('author', 'show')->name('comments.author');
            Route::patch('relationships/author', 'update')->name('comments.relationships.author.update');
        });
});

Route::prefix('articles/{article}')->group(function () {

    Route::controller(ArticleCategoryController::class)
        ->group(function () {
            Route::get('category', 'show')->name('articles.category');
            Route::get('relationships/category', 'index')->name('articles.relationships.category');
            Route::patch('relationships/category', 'update')->name('articles.relationships.category.update');
        });

    Route::controller(ArticleAuthorController::class)
        ->group(function () {
            Route::get('author', 'show')->name('articles.author');
            Route::get('relationships/author', 'index')->name('articles.relationships.author');
            Route::patch('relationships/author', 'update')->name('articles.relationships.author.update');
        });

    Route::controller(ArticleCommentsController::class)
        ->group(function () {
            Route::get('relationships/comments', 'index')->name('articles.relationships.comments');
            Route::get('comments', 'show')->name('articles.comments');
            Route::patch('relationships/comments', 'update')->name('articles.relationships.comments.update');
        });
});

Route::withoutMiddleware([ValidateJsonApiDocument::class, ValidateJsonApiHeaders::class])
    ->group(function () {
        Route::post('login', LoginController::class)
            ->name('login');

        Route::post('logout', LogoutController::class)
            ->name('logout');

        Route::post('register', RegisterController::class)
            ->name('register');
    });
