<?php

namespace App\Http\Modules\Tasks\Repositories;

use App\Models\Tasks\TaskStatus;
use Illuminate\Database\Eloquent\Collection;

class TasksStatusesRepository
{
    /**
     * @return Collection
     */
    public static function getAll(): Collection
    {
        return TaskStatus::query()
            ->select(
                'b_status as id',
                'name',
                'variant'
            )
            ->orderBy('id')
            ->get();
    }
}
