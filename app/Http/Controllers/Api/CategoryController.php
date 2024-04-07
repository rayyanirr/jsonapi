<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

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
