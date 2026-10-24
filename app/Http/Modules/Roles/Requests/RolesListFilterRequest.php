<?php

namespace App\Http\Modules\Roles\Requests;

use App\Http\Modules\Roles\Enums\GuardEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class RolesListFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['string', 'max:255'],
            'guard_name' => ['string', Rule::enum(GuardEnum::class)],
            'permission_id' => ['integer', Rule::exists(Permission::class, 'id')],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
