<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        $query = $request->input('query');
                $users = User::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->where('id', '!=', auth()->id())
            ->select('id', 'name', 'email', 'image', 'bio')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ], 200);
    }
}
