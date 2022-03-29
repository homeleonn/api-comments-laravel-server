<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;

class CommentController extends Controller
{
    public function index()
    {
        return Comment::tree();
    }

    public function store(CommentRequest $request)
    {
        return Comment::create($request->validated());
    }

    public function reply(CommentRequest $request)
    {
        return Comment::create($request->validated());
    }

    public function update(CommentRequest $request, $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->fill($request->validated());
        $comment->save();

        return $comment;
    }

    public function destroy($id)
    {
        return response(null, Comment::destroy($id) ? Response::HTTP_NO_CONTENT : Response::HTTP_NOT_FOUND);
    }
}
