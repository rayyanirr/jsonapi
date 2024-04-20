<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Article;


class ArticleController extends Controller implements HasMiddleware
{


    public static function middleware()
    {
        return [
            (new Middleware('auth:sanctum'))->only(['store', 'update', 'destroy']),
        ];
    }

    public function show($article): JsonResource
    {
        $article = Article::where('slug', $article)
            ->allowedIncludes(['category', 'author'])
            ->sparseFieldset()
            ->firstOrFail();

        return ArticleResource::make($article);
    }


    public function index(): AnonymousResourceCollection
    {
        $articles = Article::query()
            ->allowedIncludes(['category', 'author'])
            ->allowedFilters(['title', 'content', 'month', 'year', 'categories'])
            ->allowedSorts(['title', 'content'])
            ->sparseFieldset()
            ->jsonPaginate();

        return ArticleResource::collection($articles);
    }


    public function store(SaveArticleRequest $request): ArticleResource
    {
        $this->authorize('create', Article::class);

        $article = Article::create($request->validated());

        return ArticleResource::make($article);
    }

    public function update(Article $article, SaveArticleRequest $request): ArticleResource
    {
        $this->authorize('update', $article);

        $article->update($request->validated());

        return ArticleResource::make($article);
    }

    public function destroy(Article $article, Request $request): Response
    {
        $this->authorize('delete', $article);

        $article->delete();

        return response()->noContent();
    }
}
