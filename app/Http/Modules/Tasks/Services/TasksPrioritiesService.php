<?php

namespace App\Http\Modules\Tasks\Services;

use App\Http\Modules\Tasks\Repositories\TasksPrioritiesRepository;
use App\Http\Modules\Tasks\Resources\Priorities\TasksPrioritiesCollection;

class TasksPrioritiesService
{
    /**
     * @return TasksPrioritiesCollection
     */
    public function getAll(): TasksPrioritiesCollection
    {
        return new TasksPrioritiesCollection(TasksPrioritiesRepository::getAll());
    }
}
