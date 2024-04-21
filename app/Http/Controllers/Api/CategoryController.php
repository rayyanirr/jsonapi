<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->allowedFilters(['name', 'month', 'year'])
            ->allowedSorts(['name'])
            ->sparseFieldset()
            ->jsonPaginate();

        return CategoryResource::collection($categories);
    }

    public function show(string $category): JsonResource
    {
        $category = Category::whereSlug($category)->firstOrFail();

        return CategoryResource::make($category);
    }
}
