<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::prefix('auth')->group(function () {
	Route::post('/signup', [UserController::class, 'signup']);
	Route::post('/login', [UserController::class, 'login']);

	Route::middleware('auth:sanctum')->group(function () {
		Route::post('/logout', [UserController::class, 'logout']);
		Route::get('/me', [UserController::class, 'me']);
		Route::put('/me', [UserController::class, 'update']);
		Route::patch('/me', [UserController::class, 'update']);
	});
});

