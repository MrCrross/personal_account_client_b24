<?php

namespace App\Http\Modules\Roles\Requests;

use App\Http\Modules\Roles\Enums\GuardEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class RolesStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'title' => 'required|string|max:255',
            'guard_name' =>  [
                'required',
                'string',
                Rule::enum(GuardEnum::class)
            ],
            'permissions' => 'array',
            'permissions.*' => ['required', 'integer', Rule::exists(Permission::class, 'id')],
        ];
    }
}
