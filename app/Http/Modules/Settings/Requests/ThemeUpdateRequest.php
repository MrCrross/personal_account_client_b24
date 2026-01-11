<?php

namespace App\Http\Modules\Settings\Requests;

use App\Http\Modules\Settings\Enums\ProfileThemeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ThemeUpdateRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'theme' => ['required', 'string', Rule::enum(ProfileThemeEnum::class)],
        ];
    }
}
