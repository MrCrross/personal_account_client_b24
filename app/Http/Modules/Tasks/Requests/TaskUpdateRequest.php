<?php

namespace App\Http\Modules\Tasks\Requests;

use App\Http\Modules\Tasks\Dictionaries\TaskDictionary;
use App\Models\Tasks\TaskPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // BBCode, как и при создании (фронт конвертирует HTML редактора)
            'description' => ['required', 'string'],
            'priority_id' => ['required', 'integer', Rule::exists(TaskPriority::class, 'b_priority')],
        ];
    }

    public function messages(): array
    {
        return TaskDictionary::$validationMessages;
    }

    public function attributes(): array
    {
        return TaskDictionary::$validationAttributes;
    }
}
