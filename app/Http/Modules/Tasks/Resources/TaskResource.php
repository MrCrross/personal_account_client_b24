<?php

namespace App\Http\Modules\Tasks\Resources;

use App\Http\Modules\Tasks\Enums\TaskStatusesEnum;
use App\Http\Modules\Tasks\Resources\Priorities\TasksPriorityResource;
use App\Http\Modules\Tasks\Resources\Statuses\TasksStatusResource;
use App\Http\Modules\Tasks\Support\DiskFileTextParser;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Разрешённые вложения Диска: map ID => объект {id, name, is_image, url}.
     * Заполняется только в детальной выборке (TasksService::makeDetailResource) и
     * используется для вложений сообщений переписки (ID берутся из b_disk_file_ids).
     *
     * @var array<int, object>
     */
    private array $diskFiles = [];

    /**
     * Готовый набор файлов описания задачи (из b_disk_attached_object по задаче).
     *
     * @var array<int, object>
     */
    private array $descriptionFiles = [];

    /**
     * Готовые наборы файлов результатов: map b_tasks_result.ID => файлы.
     *
     * @var array<int, array<int, object>>
     */
    private array $resultFiles = [];

    /**
     * @param  array<int, object>  $diskFiles
     */
    public function withDiskFiles(array $diskFiles): static
    {
        $this->diskFiles = $diskFiles;

        return $this;
    }

    /**
     * @param  array<int, object>  $files
     */
    public function withDescriptionFiles(array $files): static
    {
        $this->descriptionFiles = $files;

        return $this;
    }

    /**
     * @param  array<int, array<int, object>>  $resultFiles
     */
    public function withResultFiles(array $resultFiles): static
    {
        $this->resultFiles = $resultFiles;

        return $this;
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): object
    {
        return (object) [
            'id' => $this->id,
            'person' => $this->person,
            'person_phone' => $this->person_phone,
            'b_id' => $this->bTask->ID,
            'title' => $this->bTask->TITLE,
            // Описание без маркеров вложений; сами файлы — в description_files
            'description' => DiskFileTextParser::strip($this->bTask->DESCRIPTION),
            'description_files' => $this->descriptionFiles,
            'status' => new TasksStatusResource($this->bTask->taskStatus),
            'priority' => new TasksPriorityResource($this->bTask->taskPriority),
            'group' => (object) [
                'id' => $this->bTask->group?->ID ?? 0,
                'name' => $this->bTask->group?->NAME ?? '',
            ],
            'responsible' => (object) [
                'id' => $this->bTask->user?->ID,
                'name' => $this->bTask->user?->NAME,
                'last_name' => $this->bTask->user?->LAST_NAME,
            ],
            'changed_date' => $this->bTask->CHANGED_DATE,
            'closed_date' => $this->bTask->CLOSED_DATE,
            'date_start' => $this->bTask->DATE_START,
            'deadline' => $this->bTask->DEADLINE,
            'start_date_plan' => $this->bTask->START_DATE_PLAN,
            'end_date_plan' => $this->bTask->END_DATE_PLAN,
            'duration_plan' => $this->bTask->DURATION_PLAN,
            'duration_fact' => $this->bTask->DURATION_FACT,
            'problem_type' => (object) [
                'id' => $this->problemType->id,
                'name' => $this->problemType->name,
            ],
            'results' => $this->bResult->map(function ($item) {
                return (object) [
                    'id' => $item->ID,
                    // Текст без маркеров вложений; сами файлы вынесены в files (как у описания)
                    'text' => DiskFileTextParser::strip($item->TEXT),
                    'files' => $this->resultFiles[$item->ID] ?? [],
                    'creator' => [
                        'id' => $item->creator?->ID,
                        'name' => $item->creator?->NAME,
                        'last_name' => $item->creator?->LAST_NAME,
                    ],
                    'created_at' => $item->CREATED_AT,
                ];
            }),
            'creator' => (object) [
                'id' => $this->user_creator_id,
                'name' => $this->creator->name,
            ],
            // Редактировать название/описание/приоритет может только создатель, пока задача не завершена
            'can_edit' => (int) $this->user_creator_id === (int) $request->user()?->getAuthIdentifier()
                && (int) $this->bTask->STATUS !== TaskStatusesEnum::COMPLETED->value,
            // Сообщения грузятся только в детальной выборке — в списке guard вернёт []
            'messages' => $this->relationLoaded('messages')
                ? $this->messages->sortBy('id')->values()->map(function ($message) {
                    return (object) [
                        'id' => $message->id,
                        'text' => $message->text,
                        'files' => $this->messageFiles($message),
                        'author' => (object) [
                            'id' => $message->user->id,
                            'name' => $message->user->name,
                        ],
                        'created_at' => $message->created_at?->toDateTimeString(),
                    ];
                })
                : [],
        ];
    }

    /**
     * Разрешённые вложения сообщения переписки (в порядке прикрепления).
     *
     * @return array<int, object>
     */
    private function messageFiles($message): array
    {
        $files = [];
        foreach ((array) ($message->b_disk_file_ids ?? []) as $id) {
            $id = (int) $id;
            if (isset($this->diskFiles[$id])) {
                $files[] = $this->diskFiles[$id];
            }
        }

        return $files;
    }
}
