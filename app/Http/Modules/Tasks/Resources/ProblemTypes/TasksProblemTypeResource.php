<?php

namespace App\Http\Modules\Tasks\Resources\ProblemTypes;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TasksProblemTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return object
     */
    public function toArray(Request $request): object
    {
        if (!empty($this->problems)) {
            $result = [
                'id' => $this->id,
                'name' => $this->name,
                'problems' => $this->problems->map(function ($problem) {
                    return new TasksProblemTypesProblemResource($problem);
                }),
            ];
        } else {
            $result = [
                'id' => $this->id,
                'name' => $this->name,
            ];
        }

        return (object)$result;
    }
}
