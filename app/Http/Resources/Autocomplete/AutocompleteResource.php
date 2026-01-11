<?php

namespace App\Http\Resources\Autocomplete;

use Illuminate\Http\Resources\Json\JsonResource;

class AutocompleteResource extends JsonResource
{
    public function toArray($request): object
    {
        if (!empty($this->id)) {
            return (object)[
                'value' => $this->id,
                'label' => $this->name,
            ];
        } elseif (!empty($this->value)) {
            return (object)[
                'value' => $this->value,
                'label' => $this->label,
            ];
        }
        return (object)[
            'value' => null,
            'label' => '',
        ];
    }
}
