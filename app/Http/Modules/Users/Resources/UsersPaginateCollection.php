<?php

namespace App\Http\Modules\Users\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UsersPaginateCollection extends ResourceCollection
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
                return new UserResource($item);
            }),
            'has_more' => $this->resource->hasMorePages(),
            'current_page' => $this->resource->currentPage(),
        ];
    }
}
