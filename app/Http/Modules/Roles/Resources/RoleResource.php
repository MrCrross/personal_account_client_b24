<?php

namespace App\Http\Modules\Roles\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray($request): object
    {
        return (object)[
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'guard_name' => $this->guard_name,
            'created_at' => $this->created_at,
            'permissions' => $this?->permissions->map(function ($permission) {
                return (object)[
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'title' => $permission->title,
                ];
            })
        ];
    }
}
