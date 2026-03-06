<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $post = Post::findOrFail($id);

        if ($post->is_readonly) {
            return response()->json(['message' => 'This post is readonly and cannot be commented on'], 403);
        }

        $comment = PostComment::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        return response()->json($comment, 201);
    }
}