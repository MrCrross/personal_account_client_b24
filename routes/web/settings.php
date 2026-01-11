<?php

use App\Http\Modules\Settings\Controllers\PasswordController;
use App\Http\Modules\Settings\Controllers\ProfileController;
use App\Http\Modules\Settings\Controllers\ThemeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/settings/profile');

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::get('/password', [PasswordController::class, 'edit'])->name('password.edit');

Route::put('/password', [PasswordController::class, 'update'])
    ->middleware('throttle:6,1')
    ->name('password.update');

Route::get('/appearance', function () {
    return Inertia::render('settings/Appearance', []);
})->name('appearance');
Route::post('/theme', [ThemeController::class, 'update'])->name('theme.update');
