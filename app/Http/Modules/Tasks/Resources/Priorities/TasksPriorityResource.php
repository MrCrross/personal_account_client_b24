<?php

namespace App\Http\Modules\Tasks\Resources\Priorities;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TasksPriorityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): object
    {
        if (! $this->resource) {
            return (object) ['id' => null, 'name' => null];
        }

        return (object) [
            'id' => $this->id ?? $this->b_priority,
            'name' => $this->name,
        ];
    }
}
