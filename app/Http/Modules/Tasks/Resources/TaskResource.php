<?php

namespace App\Http\Modules\Tasks\Resources;

use App\Http\Modules\Tasks\Resources\Priorities\TasksPriorityResource;
use App\Http\Modules\Tasks\Resources\Statuses\TasksStatusResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return object
     */
    public function toArray(Request $request): object
    {
        return (object)[
            'id' => $this->id,
            'person' => $this->person,
            'person_phone' => $this->person_phone,
            'b_id' => $this->bTask->ID,
            'title' => $this->bTask->TITLE,
            'description' => $this->bTask->DESCRIPTION,
            'status' => new TasksStatusResource($this->bTask->taskStatus),
            'priority' => new TasksPriorityResource($this->bTask->taskPriority),
            'group' => (object)[
                'id' => $this->bTask->group->ID,
                'name' => $this->bTask->group->NAME,
            ],
            'responsible' => (object)[
                'id' => $this->bTask->user->ID,
                'name' => $this->bTask->user->NAME,
                'last_name' => $this->bTask->user->LAST_NAME,
            ],
            'changed_date' => $this->bTask->CHANGED_DATE,
            'closed_date' => $this->bTask->CLOSED_DATE,
            'date_start' => $this->bTask->DATE_START,
            'deadline' => $this->bTask->DEADLINE,
            'start_date_plan' => $this->bTask->START_DATE_PLAN,
            'end_date_plan' => $this->bTask->END_DATE_PLAN,
            'duration_plan' => $this->bTask->DURATION_PLAN,
            'duration_fact' => $this->bTask->DURATION_FACT,
            'problem_type' => (object)[
                'id' => $this->problemType->id,
                'name' => $this->problemType->name,
            ],
            'results' => $this->bResult->map(static function ($item) {
                return (object)[
                    'id' => $item->ID,
                    'text' => $item->TEXT,
                    'creator' => [
                        'id' => $item->creator->ID,
                        'name' => $item->creator->NAME,
                        'last_name' => $item->creator->LAST_NAME,
                    ],
                    'created_at' => $item->CREATED_AT,
                ];
            }),
            'creator' => (object)[
                'id' => $this->user_creator_id,
                'name' => $this->creator->name,
            ],
        ];
    }
}
