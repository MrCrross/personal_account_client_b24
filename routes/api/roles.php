<?php

use App\Http\Modules\Roles\Controllers\RolesController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\PermissionMiddleware;

// Полный список ролей нужен и в управлении пользователями (форма, фильтр по роли)
Route::get('/all', [RolesController::class, 'getAll'])
    ->middleware(PermissionMiddleware::using(['roles_manage', 'users_manage']));

Route::middleware(['can:roles_manage'])->group(function () {
    Route::get('/', [RolesController::class, 'index']);
    Route::get('/permissions', [RolesController::class, 'getPermissions']);
    Route::get('/{id}', [RolesController::class, 'getById'])->whereNumber('id');
    Route::post('/', [RolesController::class, 'store']);
    Route::patch('/{id}', [RolesController::class, 'update'])->whereNumber('id');
});
