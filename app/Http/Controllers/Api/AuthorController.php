<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AuthorResource;
use App\Http\Controllers\Controller;
use App\Models\User;

class AuthorController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $authors = User::query()
            ->allowedFilters(['name', 'month', 'year'])
            ->allowedSorts(['name'])
            ->sparseFieldset()
            ->jsonPaginate();

        return AuthorResource::collection($authors);
    }

    public function show(string $author): JsonResource
    {
       $author = User::findOrFail($author);

       return AuthorResource::make($author);
    }
}
