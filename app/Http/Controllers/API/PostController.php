<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user:id,name,image', 'likes', 'comments'])
                     ->withCount('likes', 'comments')
                     ->get();
                     
        return response()->json(['posts' => $posts]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate(['content' => 'required|string', 'attachment' => 'nullable|image']);
        $post = Auth::user()->posts()->create($validatedData);
        
        return response()->json(['post' => $post], 201);
    }

    public function show($id)
    {
        try {
            $post = Post::with(['user:id,name,image', 'likes.user:id,name', 'comments.user:id,name'])
                        ->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        return response()->json(['post' => $post]);
    }

    public function update(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        if (Auth::id() !== $post->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate(['content' => 'required|string']);
        $post->update($validatedData);

        return response()->json(['post' => $post]);
    }

    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        if (Auth::id() !== $post->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $post->delete();
        return response()->json(['message' => 'Post deleted successfully']);
    }
}
