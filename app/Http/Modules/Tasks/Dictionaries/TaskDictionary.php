<?php

namespace App\Http\Modules\Tasks\Dictionaries;

class TaskDictionary
{
    public static array $validationAttributes = [
        'name' => 'название обращения',
        'description' => 'описание проблемы',
        'person' => 'контактное лицо',
        'person_phone' => 'телефон',
        'problem_type' => 'тип проблемы',
        'problem' => 'проблема',
        'priority' => 'приоритет',
        'priority_id' => 'приоритет',
        'document_number' => 'номер договора',
        'document_cost' => 'сумма договора',
        'document_contractor' => 'контрагент',
        'equipment_number' => 'номер устройства',
        'equipment_model' => 'модель устройства',
        'one_c_config' => 'конфигурация 1С',
        'one_c_version' => 'версия 1С',
        'one_c_link' => 'ссылка на обработку 1С',
        'site_link' => 'ссылка на сайт',
        'site_browser' => 'браузер',
        'site_os' => 'операционная система',
        'files' => 'файлы',
        'files.*' => 'файл',
    ];

    public static array $validationMessages = [
        // Общие сообщения
        'required' => 'Поле :attribute обязательно для заполнения',
        'required_if' => 'Поле :attribute обязательно при выбранном типе проблемы',
        'string' => 'Поле :attribute должно быть строкой',
        'integer' => 'Поле :attribute должно быть числом',
        'numeric' => 'Поле :attribute должно быть числом',
        'max' => 'Поле :attribute не должно превышать :max символов',
        'min' => 'Поле :attribute должно содержать не менее :min символов',
        'url' => 'Поле :attribute должно быть valid URL',
        'regex' => 'Поле :attribute содержит недопустимые символы',

        // Специфичные сообщения
        'name.required' => 'Название обращения обязательно',
        'name.max' => 'Название не должно превышать 255 символов',

        'description.required' => 'Описание проблемы обязательно',
        'description.min' => 'Описание должно содержать не менее 10 символов',
        'description.max' => 'Описание не должно превышать 5000 символов',

        'person.required' => 'Контактное лицо обязательно',
        'person.regex' => 'ФИО может содержать только буквы, пробелы и дефисы',

        'person_phone.required' => 'Номер телефона обязателен',
        'person_phone.regex' => 'Номер телефона имеет неверный формат',

        'problem_type.required' => 'Тип проблемы обязателен',
        'problem_type.exists' => 'Выбранный тип проблемы не существует',

        'priority.required' => 'Приоритет обязателен',
        'priority.exists' => 'Выбранный приоритет не существует',

        'problem.exists' => 'Выбранная проблема не существует',

        'document_number.required_if' => 'Номер договора обязателен для типа "Документ"',
        'document_cost.numeric' => 'Сумма договора должна быть числом',
        'document_contractor.required_if' => 'Контрагент обязателен для типа "Документ"',

        'equipment_number.required_if' => 'Номер устройства обязателен для типа "Оборудование"',

        'site_link.required_if' => 'Ссылка на сайт обязательна для типа "Сайт"',
        'site_link.url' => 'Ссылка на сайт должна быть valid URL',

        'one_c_link.url' => 'Ссылка на обработку должна быть valid URL',

        // Сообщения для файлов
        'files.max' => 'Можно загрузить не более 5 файлов',
        'files.*.file' => 'Каждый файл должен быть действительным файлом',
        'files.*.max' => 'Размер каждого файла не должен превышать 15MB',
        'files.*.mimetypes' => 'Недопустимый тип файла. Разрешены: изображения, видео, документы Office',
    ];
}
