<?php

namespace App\Http\Modules\Tasks\Enums;

/**
 * Итог редактирования обращения (TasksService::update) — контроллер маппит его в HTTP-ответ.
 */
enum TaskUpdateResultEnum: string
{
    case UPDATED = 'updated';
    case NOT_FOUND = 'not_found';
    case CLOSED = 'closed';
    case BITRIX_FAILED = 'bitrix_failed';
}
