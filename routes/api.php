<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('travel-order',       [TravelOrderController::class, 'store']);
    Route::get('travel-order/{id}',   [TravelOrderController::class, 'show']);
    Route::get('travel-order',        [TravelOrderController::class, 'index']);
    Route::patch('travel-order/{id}', [TravelOrderController::class, 'update']);
});
