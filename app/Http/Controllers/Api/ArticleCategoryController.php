<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class ArticleCategoryController extends Controller
{
    public function index(Article $article)
    {
        return CategoryResource::identifier($article->category);

    }

    public function show(Article $article)
    {
        return CategoryResource::make($article->category);

    }

    public function update(Article $article, Request $request)
    {
        $categorySlug = $request->input('data.id');

        $category = Category::where('slug', $categorySlug)->first();

        $article->update(['category_id' => $category->id]);

        return CategoryResource::identifier($article->category);

    }
}
