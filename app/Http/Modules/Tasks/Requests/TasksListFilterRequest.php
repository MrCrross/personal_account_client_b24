<?php

namespace App\Http\Modules\Tasks\Requests;

use App\Models\Tasks\TaskPriority;
use App\Models\Tasks\TaskProblemType;
use App\Models\Tasks\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TasksListFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'problem_type_id' => ['integer', Rule::exists(TaskProblemType::class, 'id')],
            'priority_id' => ['integer', Rule::exists(TaskPriority::class, 'b_priority')],
            'status_id' => ['integer', Rule::exists(TaskStatus::class, 'b_status')],
            'date_start' => ['string', 'date_format:Y-m-d'],
            'date_end' => ['string', 'date_format:Y-m-d'],
            'person_phone' => ['string'],
        ];
    }
}
