<?php

namespace App\Http\Modules\Tasks\Requests;

use App\Http\Modules\Tasks\Dictionaries\TaskDictionary;
use App\Http\Modules\Tasks\Enums\TaskProblemTypesEnum;
use App\Models\Tasks\TaskPriority;
use App\Models\Tasks\TaskProblemType;
use App\Models\Tasks\TaskProblemTypeProblem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskStoreRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' =>  ['required', 'string'],
            'person' => ['required', 'string', 'max:255'],
            'person_phone' => ['required', 'string', 'max:18'],
            'priority_id' => ['required', 'integer', Rule::exists(TaskPriority::class, 'b_priority')],
            'problem_type_id' => ['required', 'integer', Rule::exists(TaskProblemType::class, 'id')],
            'problem_id' => ['nullable', 'integer', Rule::exists(TaskProblemTypeProblem::class, 'id')],
            'document_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(
                    $this->checkProblemType(TaskProblemTypesEnum::Document)
                )
            ],
            'document_cost' => 'nullable|numeric',
            'document_contractor' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(
                    $this->checkProblemType(TaskProblemTypesEnum::Document)
                )
            ],
            'equipment_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(
                    $this->checkProblemType(TaskProblemTypesEnum::Equipment)
                )
            ],
            'equipment_model' => 'nullable|string|max:255',
            'one_c_config' => 'nullable|string|max:255',
            'one_c_version' => 'nullable|string|max:255',
            'one_c_link' => 'nullable|url|max:255',
            'site_link' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(
                    $this->checkProblemType(TaskProblemTypesEnum::Site)
                )
            ],
            'site_browser' => 'nullable|string|max:255',
            'site_os' => 'nullable|string|max:255',
            'files' => 'nullable|array|max:5',
            'files.*' => [
                'file',
                'max:15360', // 15MB
                Rule::file()->types([
                    // Изображения
                    'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg',
                    // Видео
                    'mp4', 'mov', 'avi', 'wmv', 'flv', 'webm', 'mkv',
                    // Документы Office
                    'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf',
                    'odt', 'ods', 'odp', 'txt', 'rtf'
                ])
            ],
        ];
    }

    private function checkProblemType(TaskProblemTypesEnum $problemTypeEnum): bool
    {
        return $this->post('problem_type') === $problemTypeEnum->value;
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
