<?php

namespace App\Http\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Users\Requests\UsersStoreRequest;
use App\Http\Modules\Users\Requests\UsersUpdateRequest;
use App\Http\Modules\Users\Services\UsersService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Users|Управление пользователями
 */
class UsersController extends Controller
{
    /**
     * Получить пользователей
     *
     * @response {
     *    "data": [
     *          {
     *              "id":1,
     *              "name":"ФИО",
     *              "email":"test@test.ru",
     *              "b_group":{
     *                  "id":1,
     *                  "name":"Группа"
     *              },
     *              "b_company":{
     *                  "id":1,
     *                  "name":"Компания"
     *              },
     *              "telegram_chat_id":null,
     *              "created_at":"2025-01-01 01:01:01",
     *              "roles":[
     *                  {
     *                      "id":1,
     *                      "name":"role",
     *                      "title":"Роль"
     *                  }
     *              ]
     *          }
     *      ]
     *  }
     *
     * @param Request $request
     * @param UsersService $usersService
     * @return JsonResponse
     */
    public function index(
        Request $request,
        UsersService $usersService
    ): JsonResponse {
        return response()->json($usersService->getList($request));
    }

    /**
     * Зарегистрировать пользователя
     *
     * @response {
     *   "id":1,
     *   "name":"ФИО",
     *   "email":"test@test.ru",
     *   "b_group":{
     *       "id":1,
     *       "name":"Группа"
     *   },
     *   "b_company":{
     *       "id":1,
     *       "name":"Компания"
     *   },
     *   "telegram_chat_id":null,
     *   "created_at":"2025-01-01 01:01:01",
     *   "roles":[
     *      {
     *         "id":1,
     *         "name":"role",
     *         "title":"Роль"
     *      }
     *   ]
     * }
     *
     * @param UsersStoreRequest $request
     * @param UsersService $usersService
     * @return JsonResponse
     */
    public function store(
        UsersStoreRequest $request,
        UsersService $usersService
    ): JsonResponse {
        return response()->json($usersService->store($request));
    }

    /**
     * Редактировать пользователя
     *
     * @response {
     *    "id":1,
     *    "name":"ФИО",
     *    "email":"test@test.ru",
     *    "b_group":{
     *        "id":1,
     *        "name":"Группа"
     *    },
     *    "b_company":{
     *        "id":1,
     *        "name":"Компания"
     *    },
     *    "telegram_chat_id":null,
     *    "created_at":"2025-01-01 01:01:01",
     *    "roles":[
     *       {
     *          "id":1,
     *          "name":"role",
     *          "title":"Роль"
     *       }
     *    ]
     *  }
     *
     * @param int $userId
     * @param UsersUpdateRequest $request
     * @param UsersService $usersService
     * @return JsonResponse
     */
    public function update(
        int $userId,
        UsersUpdateRequest $request,
        UsersService $usersService
    ): JsonResponse {
        return response()->json($usersService->update($userId, $request));
    }
}
