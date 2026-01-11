<?php

use App\Http\Modules\Bitrix\Controllers\BCrmCompanyController;
use App\Http\Modules\Bitrix\Controllers\BGroupController;
use Illuminate\Support\Facades\Route;

Route::middleware(['can:users_manage'])->group(function () {
    Route::get('/groups/autocomplete', [BGroupController::class, 'autocomplete']);
    Route::get('/companies/autocomplete', [BCrmCompanyController::class, 'autocomplete']);
});
