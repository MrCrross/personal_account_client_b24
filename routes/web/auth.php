<?php

use App\Http\Modules\Auth\Controllers\AuthenticatedSessionController;
use App\Http\Modules\Auth\Controllers\ConfirmablePasswordController;
use App\Http\Modules\Auth\Controllers\EmailVerificationNotificationController;
use App\Http\Modules\Auth\Controllers\EmailVerificationPromptController;
use App\Http\Modules\Auth\Controllers\NewPasswordController;
use App\Http\Modules\Auth\Controllers\PasswordResetLinkController;
use App\Http\Modules\Auth\Controllers\RegisteredUserController;
use App\Http\Modules\Auth\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');
//    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
//        ->name('password.request');
//    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
//        ->name('password.reset');
//    Route::get('register', [RegisteredUserController::class, 'create'])
//        ->name('register');
//    Route::post('register', [RegisteredUserController::class, 'store'])
//        ->name('register.store');
//    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
//        ->name('password.email');
//    Route::post('reset-password', [NewPasswordController::class, 'store'])
//        ->name('password.store');
});

Route::middleware('auth:web')->group(function () {
//    Route::get('verify-email', EmailVerificationPromptController::class)
//        ->name('verification.notice');
//    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
//        ->name('password.confirm');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
//    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
//        ->middleware(['signed', 'throttle:6,1'])
//        ->name('verification.verify');
//    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
//        ->middleware('throttle:6,1')
//        ->name('verification.send');
//    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
//        ->middleware('throttle:6,1')
//        ->name('password.confirm.store');
});
