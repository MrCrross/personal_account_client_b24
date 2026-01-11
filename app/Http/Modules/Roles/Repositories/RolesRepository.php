<?php

namespace App\Http\Modules\Roles\Repositories;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Illuminate\Contracts\Pagination\Paginator;

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

    public static function getList(): Paginator
    {
        return Role::query()
            ->select(
                'id',
                'name',
                'title',
                'guard_name',
                'created_at',
            )
            ->with('permissions')
            ->orderBy('id')
            ->simplePaginate();
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

    /**
     * @param int $id
     * @param array $fields
     * @return void
     */
    public static function update(
        int $id,
        array $fields
    ): void {
        Role::query()
            ->where('id', '=', $id)
            ->update($fields);
    }

    /**
     * @param int $id
     * @return void
     */
    public static function destroy(
        int $id
    ): void {
        Role::query()
            ->where('id', '=', $id)
            ->delete();
    }
}
