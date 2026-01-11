<?php

namespace App\Http\Modules\Roles\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Roles\Requests\RolesStoreRequest;
use App\Http\Modules\Roles\Requests\RolesUpdateRequest;
use App\Http\Modules\Roles\Services\RolesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

/**
 * @group Roles|Управление ролями
 */
class RolesController extends Controller
{
    /**
     * Получить роли пользователей по странично
     *
     * @response {
     *     "data":{
     *         {
     *             "id":1,
     *             "name":"role",
     *             "title":"Роль",
     *             "guard_name":"api",
     *             "created_at":"2025-01-01 01:01:01",
     *             "permissions":[
     *                 {
     *                     "id":1,
     *                     "name":"permission",
     *                     "title":"Право доступа"
     *                 }
     *             ]
     *         }
     *     }
     * }
     *
     * @param Request $request
     * @param RolesService $rolesService
     * @return JsonResponse
     */
    public function index(
        Request $request,
        RolesService $rolesService
    ): JsonResponse {
        return response()->json($rolesService->getList($request));
    }

    /**
     * Получить все роли пользователей
     *
     * @response {
     *      "data":{
     *          {
     *              "id":1,
     *              "name":"role",
     *              "title":"Роль",
     *              "guard_name":"api",
     *              "created_at":"2025-01-01 01:01:01",
     *              "permissions":[
     *                  {
     *                      "id":1,
     *                      "name":"permission",
     *                      "title":"Право доступа"
     *                  }
     *              ]
     *          }
     *      }
     *  }
     *
     * @param RolesService $rolesService
     * @return JsonResponse
     */
    public function getAll(
        RolesService $rolesService,
    ): JsonResponse {
        return response()->json($rolesService->getAll());
    }

    /**
     * Получить права доступа
     *
     * @response {
     *     "data":[
     *         {
     *             "id":1,
     *             "name":"permission",
     *             "title":"Право доступа",
     *             "guard_name":"api",
     *         }
     *     ]
     * }
     *
     * @return JsonResponse
     */
    public function getPermissions(): JsonResponse
    {
        $permissions = Permission::query()
            ->select(
                'id',
                'name',
                'title',
                'guard_name'
            )
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $permissions
        ]);
    }

    /**
     * Добавление роли
     *
     * @response {
     *    "id":1,
     *    "name":"role",
     *    "title":"Роль",
     *    "guard_name":"api",
     *    "created_at":"2025-01-01 01:01:01",
     *    "permissions":[
     *       {
     *         "id":1,
     *         "name":"permission",
     *         "title":"Право доступа"
     *       }
     *    ]
     * }
     *
     * @param RolesStoreRequest $request
     * @param RolesService $rolesService
     * @return JsonResponse
     */
    public function store(
        RolesStoreRequest $request,
        RolesService $rolesService
    ): JsonResponse {
        return response()->json($rolesService->store($request));
    }

    /**
     * Редактирование роли
     *
     * @response {
     *     "id":1,
     *     "name":"role",
     *     "title":"Роль",
     *     "guard_name":"api",
     *     "created_at":"2025-01-01 01:01:01",
     *     "permissions":[
     *        {
     *          "id":1,
     *          "name":"permission",
     *          "title":"Право доступа"
     *        }
     *     ]
     *  }
     *
     * @param int $roleId
     * @param RolesUpdateRequest $request
     * @param RolesService $rolesService
     * @return JsonResponse
     */
    public function update(
        int $roleId,
        RolesUpdateRequest $request,
        RolesService $rolesService
    ): JsonResponse {
        return response()->json($rolesService->update($roleId, $request));
    }
}
