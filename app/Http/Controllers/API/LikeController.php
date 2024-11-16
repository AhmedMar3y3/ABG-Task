<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Notifications\PostLiked;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class LikeController extends Controller
{
    public function store(Request $request, $postId)
    {
        try
        {        
        $post = Post::findOrFail($postId);
        } 
        catch (ModelNotFoundException $e)
     {
            return response()->json(['message' => 'Post not found'], 404);
        }
    
        $user = Auth::user();
        $like = $post->likes()->where('user_id', $user->id)->first();
    
        if ($like)
     {
            $like->delete();
            return response()->json(['message' => 'Like removed']);
        } 
        else 
        {
            $post->likes()->create(['user_id' => $user->id]);
    
            $post->user->notify(new PostLiked());
            return response()->json(['message' => 'Post liked']);
        }
    }

  public function show(Post $post)
  {
      $likes = $post->likes()->with('user:id,name')->get();
      return response()->json([
          'likes' => $likes->map(function ($like) 
          {
              return [
                  'id' => $like->user->id,
                  'name' => $like->user->name,
              ];
          }
          )
      ]);
  }
}
