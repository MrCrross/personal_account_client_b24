<?php

use App\Http\Modules\Auth\Controllers\AuthenticatedJwtController;
use App\Http\Modules\Auth\Controllers\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthenticatedJwtController::class, 'me']);
    Route::get('/refresh', [AuthenticatedJwtController::class, 'refresh']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});
