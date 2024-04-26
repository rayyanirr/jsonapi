<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleCommentsController extends Controller
{
    public function index(Article $article)
    {
        return CommentResource::identifiers($article->comments);
    }
}
