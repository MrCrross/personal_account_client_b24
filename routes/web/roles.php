<?php

use App\Http\Modules\Roles\Controllers\RolesInertiaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RolesInertiaController::class, 'index'])->name('roles.list');
Route::get('/{id}', [RolesInertiaController::class, 'view'])->whereNumber('id')->name('roles.view');
Route::get('/create', [RolesInertiaController::class, 'create'])->name('roles.create');
