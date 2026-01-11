<?php

namespace App\Http\Modules\Tasks\Services;

use App\Http\Modules\Tasks\Repositories\TasksStatusesRepository;
use App\Http\Modules\Tasks\Resources\Statuses\TasksStatusesCollection;

class TasksStatusesService
{
    /**
     * @return TasksStatusesCollection
     */
    public function getAll(): TasksStatusesCollection
    {
        return new TasksStatusesCollection(TasksStatusesRepository::getAll());
    }
}
