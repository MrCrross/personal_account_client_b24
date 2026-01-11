<?php

namespace App\Http\Modules\Users\Resources;

use App\Http\Resources\Autocomplete\AutocompleteResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): object
    {
        return (object)[
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'b_group' => new AutocompleteResource((object)[
                'id' => $this->b_group_id,
                'name' => $this->bGroup->NAME
            ]),
            'b_company' => new AutocompleteResource((object)[
                'id' => $this->b_company_id,
                'name' => $this->bCompany->TITLE
            ]),
            'telegram_chat_id' => $this->telegram_chat_id,
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString(),
            'roles' => $this?->roles->map(function ($role) {
                return (object)[
                    'id' => $role->id,
                    'name' => $role->name,
                    'title' => $role->title,
                ];
            })
        ];
    }
}
