<?php

namespace App\Http\Modules\Tasks\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TasksProblemTypesGetAllRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'with_problems' => ['boolean'],
        ];
    }
}
