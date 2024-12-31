<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CustomCors;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostMediaController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\AuthController;


// Apply CORS middleware to all API routes
Route::middleware([CustomCors::class])->group(function () {
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('chapters', ChapterController::class);
    Route::apiResource('questions', QuestionController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('post-media', PostMediaController::class);
    Route::apiResource('comments', CommentController::class);
    Route::apiResource('likes', LikeController::class);
    Route::get('/posts/{postId}/comments', [CommentController::class, 'getPostComments']);

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

});
