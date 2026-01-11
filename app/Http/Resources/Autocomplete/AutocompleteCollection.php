<?php

namespace App\Http\Resources\Autocomplete;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AutocompleteCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => $this->collection->map(static function ($autocompleteOption) {
                return new AutocompleteResource($autocompleteOption);
            }),
        ];
    }
}
