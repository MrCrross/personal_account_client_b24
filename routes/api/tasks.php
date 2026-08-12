<?php

use App\Http\Modules\Tasks\Controllers\TasksController;
use App\Http\Modules\Tasks\Controllers\TasksPrioritiesController;
use App\Http\Modules\Tasks\Controllers\TasksProblemTypesController;
use App\Http\Modules\Tasks\Controllers\TasksStatusesController;
use Illuminate\Support\Facades\Route;

Route::get('', [TasksController::class, 'getList']);
Route::get('/{id}', [TasksController::class, 'getById'])->whereNumber('id');
Route::post('', [TasksController::class, 'store']);
Route::post('/{id}/messages', [TasksController::class, 'storeMessage'])->whereNumber('id');

Route::prefix('/statuses')->group(callback: function () {
    Route::get('/', [TasksStatusesController::class, 'getAll']);
});
Route::prefix('/priorities')->group(callback: function () {
    Route::get('/', [TasksPrioritiesController::class, 'getAll']);
});
Route::prefix('/problem_types')->group(callback: function () {
    Route::get('/', [TasksProblemTypesController::class, 'getAll']);
});
