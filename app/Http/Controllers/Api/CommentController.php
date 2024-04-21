<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::paginate();

        return CommentResource::collection($comments);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Comment $comment)
    {
        return CommentResource::make($comment);
    }

    public function update(Request $request, Comment $comment)
    {
        //
    }

    public function destroy(Comment $comment)
    {
        //
    }
}
