<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\API\FriendRequestController;
use App\Http\Controllers\API\SearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//////////////////////////  User Public Routes  //////////////////////////
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

//////////////////////////  User Private Routes  //////////////////////////
Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // Profile Routes
        Route::get('/profile', [ProfileController::class, 'getProfile']);
        Route::post('/update-profile', [ProfileController::class, 'updateProfile']);
        Route::get('/view-profile/{id}', [ProfileController::class, 'viewOthersProfile']);

        // Post Routes
        Route::get('/posts', [PostController::class, 'index']);
        Route::post('/posts', [PostController::class, 'store']);
        Route::get('/posts/{post}', [PostController::class, 'show']);
        Route::put('/posts/{post}', [PostController::class, 'update']);
        Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    
        // Comment Routes
        Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
        Route::put('/posts/{post}/comments/{comment}', [CommentController::class, 'update']); 
        Route::delete('/posts/{post}/comments/{comment}', [CommentController::class, 'destroy']); 
    
        // Like Routes
        Route::post('/posts/{post}/like', [LikeController::class, 'store']); 
        Route::get('/posts/{post}/likes', [LikeController::class, 'show']); 

        // Friend Request Routes
        Route::post('/send-request/{receiverId}', [FriendRequestController::class, 'sendRequest']);
        Route::post('/accept-request/{requestId}', [FriendRequestController::class, 'acceptRequest']);
        Route::post('/reject-request/{requestId}', [FriendRequestController::class, 'rejectRequest']);
        Route::get('/friends', [FriendRequestController::class, 'listFriends']);

        // Search Route
        Route::get('/search', [SearchController::class, 'search']);
    });
    
