<?php


use App\Http\Modules\Users\Controllers\UsersInertiaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UsersInertiaController::class, 'index'])->name('users.list');
Route::get('/{id}', [UsersInertiaController::class, 'view'])->whereNumber('id')->name('users.view');
Route::get('/create', [UsersInertiaController::class, 'create'])->name('users.create');
Route::get('/{id}/edit', [UsersInertiaController::class, 'edit'])->whereNumber('id')->name('users.edit');
