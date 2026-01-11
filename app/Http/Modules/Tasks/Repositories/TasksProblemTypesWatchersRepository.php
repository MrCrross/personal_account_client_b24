<?php

namespace App\Http\Modules\Tasks\Repositories;

use App\Models\Tasks\TaskProblemTypeWatcher;
use Illuminate\Support\Collection;

class TasksProblemTypesWatchersRepository
{
    public static function getBUserIdByProblemTypId(int $problemTypeId): Collection
    {
        return TaskProblemTypeWatcher::query()
            ->select(
                'b_user_id'
            )
            ->where('problem_type_id', '=', $problemTypeId)
            ->pluck('b_user_id');
    }
}
