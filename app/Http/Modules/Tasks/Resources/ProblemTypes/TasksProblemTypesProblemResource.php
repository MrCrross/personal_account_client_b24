<?php

namespace App\Http\Modules\Tasks\Resources\ProblemTypes;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TasksProblemTypesProblemResource extends JsonResource
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
            'problem_type_id' => $this->problem_type_id
        ];
    }
}
