<?php

namespace App\Http\Modules\Tasks\Resources\Statuses;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TasksStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): object
    {
        return (object)[
            'id' => $this->id,
            'name' => $this->name,
            'variant' => $this->variant,
        ];
    }
}
