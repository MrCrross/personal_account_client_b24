<?php

namespace App\Http\Modules\Tasks\Resources\ProblemTypes;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TasksProblemTypesCollection extends ResourceCollection
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
                return new TasksProblemTypeResource($item);
            }),
        ];
    }
}
