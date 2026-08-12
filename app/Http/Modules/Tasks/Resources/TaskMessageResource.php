<?php

namespace App\Http\Modules\Tasks\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskMessageResource extends JsonResource
{
    /**
     * Разрешённые вложения сообщения: объекты {id, name, is_image, url}.
     * Контент в БД не храним — резолвим из Битрикс-Диска (TasksService::resolveDiskFiles).
     *
     * @var array<int, object>
     */
    private array $files = [];

    /**
     * @param  array<int, object>  $files
     */
    public function withFiles(array $files): static
    {
        $this->files = $files;

        return $this;
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): object
    {
        return (object) [
            'id' => $this->id,
            'text' => $this->text,
            'files' => array_values($this->files),
            'author' => (object) [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
