<?php

namespace App\Http\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Auth\Requests\ConfirmablePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password page.
     */
    public function show(): Response
    {
        return Inertia::render('auth/ConfirmPassword', []);
    }

    /**
     * Confirm the user's password.
     *
     * @param ConfirmablePasswordRequest $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(ConfirmablePasswordRequest $request): RedirectResponse
    {
        $request->checkPassword();

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
