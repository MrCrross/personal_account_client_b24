<?php

namespace App\Http\Modules\Roles\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Roles\Requests\RolesListFilterRequest;
use App\Http\Modules\Roles\Requests\RolesStoreRequest;
use App\Http\Modules\Roles\Requests\RolesUpdateRequest;
use App\Http\Modules\Roles\Services\RolesService;
use Illuminate\Http\JsonResponse;
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
     */
    public function index(
        RolesListFilterRequest $request,
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
            'data' => $permissions,
        ]);
    }

    /**
     * Получить роль
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
     * @response 404 {"message": "Not Found"}
     */
    public function getById(
        int $id,
        RolesService $rolesService
    ): JsonResponse {
        $role = $rolesService->getById($id);
        abort_if($role === null, 404);

        return response()->json($role);
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
     */
    public function update(
        int $roleId,
        RolesUpdateRequest $request,
        RolesService $rolesService
    ): JsonResponse {
        return response()->json($rolesService->update($roleId, $request));
    }
}
