<?php

namespace App\Http\Modules\Users\Requests;

use App\Models\Bitrix24\BSonetGroup;
use App\Models\Bitrix24\Crm\BCrmCompany;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UsersStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore(Auth::user()?->getAuthIdentifier())
            ],
            'b_group_id' => ['required', 'integer', Rule::exists(BSonetGroup::class, 'ID')],
            'b_company_id' => ['required', 'integer', Rule::exists(BCrmCompany::class, 'ID')],
            'telegram_chat_id' => ['nullable', 'string'],
            'password' => 'required|string|min:8',
            'roles' => 'nullable|array',
            'roles.*' => ['required', 'integer', Rule::exists(Role::class, 'id')],
        ];
    }
}
