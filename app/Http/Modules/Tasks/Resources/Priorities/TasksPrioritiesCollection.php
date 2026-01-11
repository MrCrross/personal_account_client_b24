<?php

namespace App\Http\Modules\Tasks\Resources\Priorities;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TasksPrioritiesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(static function ($item) {
                return new TasksPriorityResource($item);
            }),
        ];
    }
}
