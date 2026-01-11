<?php

use App\Http\Modules\Roles\Controllers\RolesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RolesController::class, 'index']);
Route::get('/all', [RolesController::class, 'getAll']);
Route::get('/permissions', [RolesController::class, 'getPermissions']);
Route::get('/{id}', [RolesController::class, 'getById'])->whereNumber('id');
Route::post('/', [RolesController::class, 'store']);
Route::patch('/{id}', [RolesController::class, 'update'])->whereNumber('id');

