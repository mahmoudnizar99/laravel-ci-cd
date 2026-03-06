<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'comments.user'])->latest()->get();
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $post = Post::create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return response()->json($post, 201);
    }

    public function makeReadonly($id)
    {
        $post = Post::findOrFail($id);
        $post->update(['is_readonly' => true]);

        return response()->json(['message' => 'Post is now readonly']);
    }
}