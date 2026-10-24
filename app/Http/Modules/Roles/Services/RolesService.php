<?php

namespace App\Http\Modules\Roles\Services;

use App\Http\Modules\Roles\Repositories\PermissionsRepository;
use App\Http\Modules\Roles\Repositories\RolesRepository;
use App\Http\Modules\Roles\Resources\RoleResource;
use App\Http\Modules\Roles\Resources\RolesCollection;
use App\Http\Modules\Roles\Resources\RolesPaginateCollection;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RolesService
{
    public function getAll(): RolesCollection
    {
        return new RolesCollection(RolesRepository::getAll());
    }

    public function getList(
        Request $request
    ): RolesPaginateCollection {
        $rolesList = RolesRepository::getList(
            search: $request->query('search'),
            guardName: $request->query('guard_name'),
            permissionId: $request->query('permission_id'),
            perPage: $request->query('per_page', 15)
        );

        return new RolesPaginateCollection($rolesList);
    }

    public function getById(int $roleId): ?RoleResource
    {
        $role = RolesRepository::getById($roleId);
        if (empty($role)) {
            return null;
        }
        $role->load('permissions');

        return new RoleResource($role);
    }

    public function store(Request $request): RoleResource
    {
        $fields = [
            'name' => $request->post('name'),
            'title' => $request->post('title'),
            'guard_name' => $request->post('guard_name'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
        $role = RolesRepository::store($fields);

        if ($request->has('permissions') && $request->filled('permissions')) {
            $permissions = PermissionsRepository::getByIds($request->post('permissions'));
            $role->syncPermissions($permissions);
        }

        return new RoleResource($role);
    }

    public function update(int $roleId, Request $request): RoleResource
    {
        $fields = [
            'name' => $request->post('name'),
            'title' => $request->post('title'),
            'guard_name' => $request->post('guard_name'),
        ];

        RolesRepository::update($roleId, $fields);
        $role = RolesRepository::getById($roleId);

        if ($request->has('permissions')) {
            if (is_array($request->post('permissions'))) {
                $permissions = PermissionsRepository::getByIds($request->post('permissions'));
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }
        }

        return new RoleResource($role);
    }

    public function destroy(int $roleId): void
    {
        RolesRepository::destroy($roleId);
    }
}
