<?php

namespace App\Http\Modules\Tasks\Repositories;

use App\Models\Tasks\TaskPriority;
use Illuminate\Database\Eloquent\Collection;

class TasksPrioritiesRepository
{
    /**
     * @return Collection
     */
    public static function getAll(): Collection
    {
        return TaskPriority::query()
            ->select(
                'b_priority as id',
                'name',
            )
            ->orderBy('id')
            ->get();
    }
}
