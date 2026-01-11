<?php

use App\Http\Modules\Tasks\Controllers\TasksInertiaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TasksInertiaController::class, 'index'])->name('tasks.list');
Route::get('/create', [TasksInertiaController::class, 'create'])->name('tasks.create');
