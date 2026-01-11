<?php

use App\Http\Modules\Users\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UsersController::class, 'index']);
Route::get('/{id}', [UsersController::class, 'getById'])->whereNumber('id');
Route::post('/', [UsersController::class, 'store']);
Route::patch('/{id}', [UsersController::class, 'update'])->whereNumber('id');

