<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Store;
use App\Http\Requests\User\Login;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    public function register(Store $request)
    { 
        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);
        if ($request->hasFile('image')) {
            $imageName = $request->file('image')->getClientOriginalName();
    
            $request->file('image')->move(public_path('images/users/'), $imageName);
    
            $validatedData['image'] = $imageName;
        }
        $user = User::create($validatedData);     
        $user->save();
        return response()->json([ 
            'message' => 'Registration successful, You can now login to your account.'
        ], 201);
    }

public function login(Login $request)
{
    $validatedData = $request->validated();
    $user = User::where('email', $request->input('email'))->first();
    if (!$user || !Hash::check($request->input('password'), $user->password))
    {
        return response()->json( "Credentials do not match", 404);
    }
    $token = $user->createToken('Api token of ' . $user->name)->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token
    ], 200);
}


   public function logout(){

       Auth::user()->tokens()->delete();
       return response()->json(
        (Auth::user()->name .' ,you have successfully logged out and your token has been deleted'),200
       );
   }

   public function forgotPassword(Request $request)
   {
       $request->validate(['email' => 'required|email']);
   
       $user = User::where('email', $request->email)->first();
   
       if (!$user) {
           return response()->json('User not found', 404);
       }
   
       $code = mt_rand(100000, 999999);
       DB::table('password_reset_tokens')->updateOrInsert(
           ['email' => $request->email],
           [
               'token' => $code,
               'created_at' => now()
           ]
       );
   
       Mail::raw("Your password reset code is: $code", function ($message) use ($user) {
           $message->to($user->email)
               ->subject('Password Reset Code');
       });
   
       return response()->json( 'Password reset code sent to your email', 200);
   }

public function resetPassword(Request $request)
{
   $request->validate([
       'email' => 'required|email',
       'code' => 'required|numeric',
       'password' => 'required|confirmed',
   ]);

   $resetEntry = DB::table('password_reset_tokens')
       ->where('email', $request->email)
       ->where('token', $request->code)
       ->first();

 if (!$resetEntry) {
        return response()->json( 'Invalid code',  400); 
      }
   $user = User::where('email', $request->email)->first();
   $user->password = Hash::make($request->password);
   $user->save();

   DB::table('password_reset_tokens')->where('email', $request->email)->delete();
   return response()->json( 'Password has been reset successfully'
, 200);
}
 
}
