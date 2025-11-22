<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/chat', [ChatController::class, 'chat']);  // unlimited prompts
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post('/guest-chat', [ChatController::class, 'guestChat']);
