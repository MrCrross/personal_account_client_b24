<?php

namespace App\Http\Modules\Tasks\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskMessageStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Текст обязателен, только если к сообщению не приложены файлы
            'text' => ['required_without:files', 'nullable', 'string', 'max:5000'],
            'files' => ['nullable', 'array', 'max:5'],
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
                    'odt', 'ods', 'odp', 'txt', 'rtf',
                ]),
            ],
        ];
    }
}
