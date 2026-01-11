<?php

namespace App\Http\Modules\Tasks\Repositories;

use App\Models\Tasks\TaskProblemType;
use Illuminate\Database\Eloquent\Collection;

class TasksProblemTypesRepository
{
    /**
     * @return Collection
     */
    public static function getAll(): Collection
    {
        return TaskProblemType::query()
            ->select(
                'id',
                'name'
            )
            ->get();
    }

    /**
     * @param int $id
     * @param bool $withProblems - присоединить проблемы типа проблемы
     * @param bool $withPerformers - присоединить исполнителей типа проблемы
     * @param bool $withWatchers - присоединить наблюдателей типа проблемы
     * @return TaskProblemType
     */
    public static function getById(
        int $id,
        bool $withProblems = false,
        bool $withPerformers = false,
        bool $withWatchers = false
    ): TaskProblemType {
        $with = [];
        if ($withProblems) {
            $with[] = 'problems';
        }
        if ($withPerformers) {
            $with[] = 'performers';
        }
        if ($withWatchers) {
            $with[] = 'watchers';
        }

        return TaskProblemType::query()
            ->select(
                'id',
                'name'
            )
            ->with($with)
            ->where('id', '=', $id)
            ->first();
    }

    /**
     * @return Collection
     */
    public static function getAllWithProblems(): Collection
    {
        return TaskProblemType::query()
            ->select(
                'id',
                'name'
            )
            ->with('problems')
            ->get();
    }
}
