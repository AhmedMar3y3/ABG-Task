<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FriendRequest;
use Illuminate\Support\Facades\Auth;
use App\Notifications\FriendRequestSent;
use App\Models\User;

class FriendRequestController extends Controller
{
    public function sendRequest(Request $request, $receiverId)
    {
        $senderId = Auth::id();

        if (FriendRequest::where('sender_id', $senderId)->where('receiver_id', $receiverId)->exists()) {
            return response()->json(['message' => 'Friend request already sent'], 409);
        }

        FriendRequest::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'status' => 'pending'
        ]);

        $receiver = User::find($receiverId);
        if ($receiver) {
            $receiver->notify(new FriendRequestSent());
        }

        return response()->json(['message' => 'Friend request sent']);
    }

    public function acceptRequest($requestId)
    {
        $request = FriendRequest::where('id', $requestId)->where('receiver_id', Auth::id())->first();

        if (!$request || $request->status !== 'pending') {
            return response()->json(['message' => 'Invalid request'], 404);
        }

        $request->update(['status' => 'accepted']);
        return response()->json(['message' => 'Friend request accepted']);
    }

    public function rejectRequest($requestId)
    {
        $request = FriendRequest::where('id', $requestId)->where('receiver_id', Auth::id())->first();

        if (!$request || $request->status !== 'pending') {
            return response()->json(['message' => 'Invalid request'], 404);
        }

        $request->update(['status' => 'rejected']);
        return response()->json(['message' => 'Friend request rejected']);
    }

    public function listFriends()
    {
        $friends = Auth::user()->friends;
        return response()->json($friends);
    }
}
