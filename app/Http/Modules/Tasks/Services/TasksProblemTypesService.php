<?php

namespace App\Http\Modules\Tasks\Services;

use App\Http\Modules\Tasks\Repositories\TasksProblemTypesRepository;
use App\Http\Modules\Tasks\Resources\ProblemTypes\TasksProblemTypesCollection;

class TasksProblemTypesService
{
    /**
     * @param bool $withProblems
     * @return TasksProblemTypesCollection
     */
    public function getAll(bool $withProblems = false): TasksProblemTypesCollection
    {
        if ($withProblems) {
            $problemTypes = TasksProblemTypesRepository::getAllWithProblems();
        } else {
            $problemTypes = TasksProblemTypesRepository::getAll();
        }

        return new TasksProblemTypesCollection($problemTypes);
    }
}
