<?php

namespace App\Http\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Auth\Requests\LoginRequest;
use App\Http\Modules\Auth\Resources\JwtTokenResource;
use App\Http\Modules\Users\Repositories\UsersRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * @group Authorization|Авторизация
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Авторизация пользователя
     *
     * @unauthenticated
     *
     * @response {
     *  "error": false,
     *  "data": {
     *     "user": {
     *      "id": 1,
     *      "name": "Test",
     *      "email": "test@test.com",
     *      "telegram_chat_id": null,
     *      "b_group_id": 12,
     *      "b_company_id": 11,
     *      "theme": "light",
     *      "created_at": "2025-01-01 02:30:00",
     *      "updated_at": null
     *      },
     *     "access_token": "123123123123",
     *     "token_type": "Bearer",
     *     "expires_on": 3600
     *  }
     * }
     * @response 401 {"error":true, "message": "Неверная почта или пароль", "data": []}
     *
     * @throws ValidationException
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();
        if (empty($request->session())) {
            $request->session()->regenerate();
        }

        $token = Auth::guard('api')->login(UsersRepository::getByEmail($request->post('email')));

        return response()->json(new JwtTokenResource(['token' => $token]));
    }

    /**
     * Удалить авторизационную сессию (выйти).
     */
    public function destroy(Request $request): void
    {
        Auth::guard('api')->logout();

        if (! empty($request->session())) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }
}
