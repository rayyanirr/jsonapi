<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthorController;
use Illuminate\Support\Facades\Route;

Route::apiResource('articles', ArticleController::class);
Route::apiResource('categories', CategoryController::class)->only('index', 'show');
Route::apiResource('authors', AuthorController::class)->only('index', 'show');


Route::get('articles/{author}/relationships/author', fn() => 'TODO')->name('articles.relationships.author');
Route::get('articles/{author}/author', fn() => 'TODO')->name('articles.author');

Route::get('articles/{article}/relationships/category', fn() => 'TODO')->name('articles.relationships.category');
Route::get('articles/{article}/category', fn() => 'TODO')->name('articles.category');
