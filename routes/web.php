<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Permission\Middleware\PermissionMiddleware;

Route::get('/', function () {
    if (Auth::hasUser()) {
        return response()->redirectToRoute('tasks.list');
    } else {
        return response()->redirectToRoute('login');
    }
})->name('home');

Route::middleware('auth:web')->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard', []);
    })->name('dashboard');
    Route::get('policies/cookies', function () {
        return Inertia::render('policies/Cookies', []);
    })->name('policies.cookies');
    Route::get('policies/confidential', function () {
        return Inertia::render('policies/Confidential', []);
    })->name('policies.confidential');
    Route::prefix('settings')->group(base_path('routes/web/settings.php'));
    Route::prefix('tasks')->group(base_path('routes/web/tasks.php'));
    Route::prefix('admin')->group(function () {
        Route::get('/', function () {
            return Inertia::render('AdminPanel', []);
        })
            ->middleware(PermissionMiddleware::using(['users_module', 'roles_module']))
            ->name('admin');
        Route::prefix('users')
            ->middleware('can:users_module')
            ->group(base_path('routes/web/users.php'));
        Route::prefix('roles')
            ->middleware('can:roles_module')
            ->group(base_path('routes/web/roles.php'));
    });
});
require __DIR__.'/web/auth.php';
