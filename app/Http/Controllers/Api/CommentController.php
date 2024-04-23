<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Requests\SaveCommentRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CommentController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            (new Middleware('auth:sanctum'))->only(['store', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $comments = Comment::paginate();

        return CommentResource::collection($comments);
    }

    public function store(SaveCommentRequest $request)
    {
        $comment = new Comment;
        $comment->body = $request->input('data.attributes.body');
        $comment->user_id = $request->getRelationshipId('author');
        $articleSlug = Article::whereSlug($request->getRelationshipId('article'))->first();
        $comment->article_id = $articleSlug->id;
        $comment->save();

        return CommentResource::make($comment);
    }

    public function show(Comment $comment)
    {
        return CommentResource::make($comment);

    }

    public function update(SaveCommentRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->body = $request->input('data.attributes.body');

        if ($request->hasRelationship('article')) {

            $articleSlug = Article::whereSlug($request->getRelationshipId('article'))->first();
            $comment->article_id = $articleSlug->id;
        }

        if ($request->hasRelationship('author')) {

            $comment->author_id = $request->getRelationshipId('author');
        }

        $comment->save();

        return CommentResource::make($comment);
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->noContent();
    }
}
