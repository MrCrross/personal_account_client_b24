<?php

namespace App\Http\Modules\Roles\Repositories;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class RolesRepository
{
    public static function getAll(): Collection
    {
        return Role::query()
            ->select(
                'id',
                'name',
                'title',
                'guard_name',
            )
            ->get();
    }

    public static function getList(
        ?string $search,
        ?string $guardName,
        ?int $permissionId,
        int $perPage = 15
    ): Paginator {
        return Role::query()
            ->select(
                'id',
                'name',
                'title',
                'guard_name',
                'created_at',
            )
            ->with('permissions')
            ->when(! empty($search), function ($query) use ($search) {
                $like = '%'.addcslashes($search, '%_\\').'%';

                return $query->where(function ($q) use ($like) {
                    $q->where('title', 'LIKE', $like)
                        ->orWhere('name', 'LIKE', $like);
                });
            })
            ->when(! empty($guardName), function ($query) use ($guardName) {
                return $query->where('guard_name', '=', $guardName);
            })
            ->when(! empty($permissionId), function ($query) use ($permissionId) {
                return $query->whereHas('permissions', function ($q) use ($permissionId) {
                    $q->whereKey($permissionId);
                });
            })
            ->orderBy('id')
            ->simplePaginate($perPage);
    }

    public static function getByIds(array $ids): Collection
    {
        return Role::query()
            ->whereIn('id', $ids)
            ->get();
    }

    public static function getById(
        string $id
    ): ?Role {
        return Role::query()
            ->where('id', '=', $id)
            ->first();
    }

    public static function store(array $fields): Role
    {
        return Role::query()->create($fields);
    }

    public static function update(
        int $id,
        array $fields
    ): void {
        Role::query()
            ->where('id', '=', $id)
            ->update($fields);
    }

    public static function destroy(
        int $id
    ): void {
        Role::query()
            ->where('id', '=', $id)
            ->delete();
    }
}
