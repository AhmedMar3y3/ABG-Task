<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Notifications\CommentAdded;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validatedData = $request->validate(['content' => 'required|string']);
        $comment = $post->comments()->create([
            'content' => $validatedData['content'],
            'user_id' => Auth::id(),
        ]);
        $user = $post->user;
        $user->notify(new CommentAdded( ));
        return response()->json(['comment' => $comment], 201);
    }
    public function update(Request $request, Post $post, $commentId)
{
    $validatedData = $request->validate(['content' => 'required|string']);
    $comment = $post->comments()->where('id', $commentId)->where('user_id', Auth::id())->firstOrFail();
    $comment->update(['content' => $validatedData['content']]);
    return response()->json(['comment' => $comment], 200);
}

public function destroy($postId, $commentId)
{

        $post = Post::findOrFail($postId);
        $comment = Comment::findOrFail($commentId);
        if (auth()->id() === $post->user_id || auth()->id() === $comment->user_id) {
            $comment->delete();
            return response()->json(['message' => 'Comment has been deleted successfully'], 200);
        } else {
        return response()->json(['message' => 'You are not authorized to delete this comment'], 403);

    }
}
}

