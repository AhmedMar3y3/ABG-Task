<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\User\Update;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        $user = Auth::user();
        return response()->json(['user' => $user], 200);
    }
    public function updateProfile(Update $request)
    {
        $user = Auth::user();
        $updateData = $request->only(['name', 'bio']);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/users'), $imageName);
            $updateData['image'] = url('public/images/users/' . $imageName);
        }

        $user->update($updateData);
        return response()->json(['user' => $user, 'message' => 'Profile updated successfully'], 200);
    }
    public function viewOthersProfile($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json([
            'user' => [
            'name' => $user->name,
            'bio' => $user->bio,
            'image' => $user->image,
            'email' => $user->email,
            ]
        ], 200);
    }
}
