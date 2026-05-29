<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VerifyEmailController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\PlantTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/signup', [UserController::class, 'signup']);
    Route::post('/login', [UserController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [UserController::class, 'logout']);
        Route::get('/me', [UserController::class, 'me']);
        Route::put('/me', [UserController::class, 'update']);
        Route::patch('/me', [UserController::class, 'update']);
        Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
            ->middleware('throttle:6,1')
            ->name('verification.send');
    });
});

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/devices', [DeviceController::class, 'index']);
    Route::post('/devices/map', [DeviceController::class, 'map']);
    Route::get('/devices/{device}', [DeviceController::class, 'show']);
    Route::put('/devices/{device}', [DeviceController::class, 'update']);
    Route::post('/devices/{device}/unmap', [DeviceController::class, 'unmap']);

    Route::get('/plant-types', [PlantTypeController::class, 'index']);
    Route::get('/plant-types/{plantType}', [PlantTypeController::class, 'show']);
    Route::get('/plant-types/{plantType}/image', [PlantTypeController::class, 'image']);
});
