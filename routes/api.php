<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(base_path('routes/api/auth.php'));

Route::middleware('auth:api')->group(callback: function () {
    Route::prefix('tasks')->group(base_path('routes/api/tasks.php'));
    Route::prefix('bitrix')->group(base_path('routes/api/bitrix.php'));
    Route::prefix('users')->group(base_path('routes/api/users.php'));
    Route::prefix('roles')->group(base_path('routes/api/roles.php'));
});
