<?php

namespace App\Http\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Settings\Requests\ThemeUpdateRequest;
use App\Http\Modules\Users\Repositories\UsersRepository;
use Illuminate\Support\Facades\Auth;

class ThemeController extends Controller
{
    /**
     * @param ThemeUpdateRequest $request
     * @return void
     */
    public function update(
        ThemeUpdateRequest $request
    ): void {
        UsersRepository::update(Auth::guard('web')->id(), [
            'theme' => $request->post('theme')
        ]);
    }
}
