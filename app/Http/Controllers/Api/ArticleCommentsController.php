<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;

class ArticleCommentsController extends Controller
{
    public function index(Article $article)
    {
        return CommentResource::identifiers($article->comments);
    }

    public function show(Article $article)
    {
        return CommentResource::collection($article->comments);
    }

    public function update(Article $article, Request $request)
    {
        $commentsIds = $request->input('data.*.id');

        $comments = Comment::find($commentsIds);

        $comments->each->update([

            'article_id' => $article->id,

        ]);

        return CommentResource::identifiers($article->comments);
    }
}
