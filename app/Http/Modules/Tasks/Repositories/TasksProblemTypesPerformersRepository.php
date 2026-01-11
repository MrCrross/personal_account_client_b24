<?php

namespace App\Http\Modules\Tasks\Repositories;

use App\Models\Tasks\TaskProblemTypePerformer;
use Illuminate\Support\Collection;

class TasksProblemTypesPerformersRepository
{
    public static function getBUserIdByProblemTypId(int $problemTypeId): Collection
    {
        return TaskProblemTypePerformer::query()
            ->select(
                'b_user_id'
            )
            ->where('problem_type_id', '=', $problemTypeId)
            ->pluck('b_user_id');
    }
}
