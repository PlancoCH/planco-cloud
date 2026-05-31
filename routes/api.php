<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VerifyEmailController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\DeviceApiController;
use App\Http\Controllers\Api\PlantTypeController;
use App\Http\Controllers\Api\PlantController;
use App\Http\Controllers\Api\DailyInsightController;
use App\Http\Controllers\Api\PlantDataController;
use App\Http\Middleware\VerifyDeviceApiKey;
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

    Route::get('/plants', [PlantController::class, 'index']);
    Route::post('/plants', [PlantController::class, 'store']);
    Route::get('/plants/{plant}', [PlantController::class, 'show']);
    Route::put('/plants/{plant}', [PlantController::class, 'update']);
    Route::delete('/plants/{plant}', [PlantController::class, 'destroy']);

    Route::post('/plants/{plant}/map', [PlantController::class, 'map']);
    Route::post('/plants/{plant}/unmap', [PlantController::class, 'unmap']);

    Route::post('/plants/{plant}/share', [PlantController::class, 'share']);
    Route::delete('/plants/{plant}/share', [PlantController::class, 'revokeShare']);
    Route::post('/plants/join', [PlantController::class, 'join']);

    Route::get('/plants/{plant}/insights', [DailyInsightController::class, 'index']);
    Route::get('/plants/{plant}/insights/{dailyInsight}', [DailyInsightController::class, 'show']);
    Route::patch('/plants/{plant}/insights/{dailyInsight}/read', [DailyInsightController::class, 'markAsRead']);

    Route::get('/plants/{plant}/data', [PlantDataController::class, 'index']);
    Route::get('/plants/{plant}/data/{plantData}', [PlantDataController::class, 'show']);
});

Route::middleware(VerifyDeviceApiKey::class)->prefix('device-api')->group(function () {
    Route::post('/data', [DeviceApiController::class, 'storeData']);
    Route::put('/wifi-rssi', [DeviceApiController::class, 'updateWifiRssi']);
    Route::get('/config', [DeviceApiController::class, 'getConfig']);
});

