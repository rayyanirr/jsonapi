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
use App\Models\Category;

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

        $data = $request->validated()['data'];

        $articleData = $data['attributes'];

        $articleData['user_id'] = $data['relationships']['author']['data']['id'];

        $categorySlug = $data['relationships']['category']['data']['id'];
        $category = Category::whereSlug( $categorySlug)->first();

        $articleData['category_id'] = $category->id;

        $article = Article::create($articleData);

        return ArticleResource::make($article);
    }

    public function update(Article $article, SaveArticleRequest $request): ArticleResource
    {
        $this->authorize('update', $article);

        $data = $request->validated()['data'];

        $articleData = $data['attributes'];

        if (isset($articleData['relationships'])) {

            if (isset($data['relationships']['author'])) {

                $articleData['user_id'] = $data['relationships']['author']['data']['id'];
            }

            if ($data['relationships']['category']) {

                $categorySlug = $data['relationships']['category']['data']['id'];

                $category = Category::whereSlug( $categorySlug)->first();

                $articleData['category_id'] = $category->id;
            }



        }


        $article->update($articleData);

        return ArticleResource::make($article);
    }

    public function destroy(Article $article, Request $request): Response
    {
        $this->authorize('delete', $article);

        $article->delete();

        return response()->noContent();
    }
}
