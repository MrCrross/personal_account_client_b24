<?php

use App\Http\Modules\Tasks\Controllers\TasksInertiaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TasksInertiaController::class, 'index'])->name('tasks.list');
Route::get('/create', [TasksInertiaController::class, 'create'])->name('tasks.create');
Route::get('/disk-file/{fileId}', [TasksInertiaController::class, 'diskFile'])->whereNumber('fileId')->name('tasks.disk-file');
Route::get('/{id}', [TasksInertiaController::class, 'show'])->whereNumber('id')->name('tasks.show');
