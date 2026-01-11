<?php

namespace App\Http\Modules\Bitrix\Repositories;

use App\Models\Bitrix24\Crm\BCrmContact;

/**
 * НЕ ИСПОЛЬЗОВАТЬ ДЛЯ ВСТАВКИ ДАННЫХ
 * ЛЮБОЕ НЕ ПОЛУЧЕНИЕ ЧЕРЕЗ BWebhookConstructor
 */
class BCrmContactRepository
{
    public static function searchContact(string $person, string $personPhone): ?BCrmContact
    {
        return BCrmContact::query()
            ->select(
                'ID as id',
                'SEARCH_CONTENT as content'
            )
            ->where('SEARCH_CONTENT', 'like', '%' . $person . '%')
            ->orWhere('SEARCH_CONTENT', 'like', '%' . $personPhone . '%')
            ->first();
    }
}
