<?php

namespace App\Http\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Users\Requests\UsersListFilterRequest;
use App\Http\Modules\Users\Requests\UsersStoreRequest;
use App\Http\Modules\Users\Requests\UsersUpdateRequest;
use App\Http\Modules\Users\Services\UsersService;
use Illuminate\Http\JsonResponse;

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
     */
    public function index(
        UsersListFilterRequest $request,
        UsersService $usersService
    ): JsonResponse {
        return response()->json($usersService->getList($request));
    }

    /**
     * Получить пользователя
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
     * @response 404 {"message": "Not Found"}
     */
    public function getById(
        int $id,
        UsersService $usersService
    ): JsonResponse {
        $user = $usersService->getById($id);
        abort_if($user === null, 404);

        return response()->json($user);
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
     */
    public function update(
        int $userId,
        UsersUpdateRequest $request,
        UsersService $usersService
    ): JsonResponse {
        return response()->json($usersService->update($userId, $request));
    }
}
