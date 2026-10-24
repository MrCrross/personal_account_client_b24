<?php

namespace App\Http\Modules\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UsersListFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['string', 'max:255'],
            'role_id' => ['integer', Rule::exists(Role::class, 'id')],
            'b_company_id' => ['integer'],
            'date_start' => ['string', 'date_format:Y-m-d'],
            'date_end' => ['string', 'date_format:Y-m-d'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
