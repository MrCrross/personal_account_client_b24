<?php

namespace App\Http\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Auth\Resources\JwtTokenResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @group Authorization|Авторизация
 */
class AuthenticatedJwtController extends Controller
{
    /**
     * Получить данные авторизированного пользователя
     * @response {
     *    "id": 1,
     *    "name": "Test",
     *    "email": "test@test.com",
     *    "telegram_chat_id": null,
     *    "b_group_id": 12,
     *    "b_company_id": 11,
     *    "theme": "light",
     *    "created_at": "2025-01-01 02:30:00",
     *    "updated_at": null
     *  }
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json(Auth::user());
    }

    /**
     * Обновить токен авторизации
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
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return response()->json(new JwtTokenResource(['token' => Auth::refresh()]));
    }
}
