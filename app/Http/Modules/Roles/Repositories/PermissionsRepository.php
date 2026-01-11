<?php

namespace App\Http\Modules\Roles\Repositories;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

class PermissionsRepository
{
    public static function getByIds(array $ids): Collection
    {
        return Permission::query()
            ->whereIn('id', $ids)
            ->get();
    }
}
