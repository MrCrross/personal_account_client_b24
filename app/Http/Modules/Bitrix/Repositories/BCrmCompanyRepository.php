<?php

namespace App\Http\Modules\Bitrix\Repositories;

use App\Models\Bitrix24\BSonetGroup;
use App\Models\Bitrix24\Crm\BCrmCompany;
use Illuminate\Database\Eloquent\Collection;

/**
 * НЕ ИСПОЛЬЗОВАТЬ ДЛЯ ВСТАВКИ ДАННЫХ
 * ЛЮБОЕ НЕ ПОЛУЧЕНИЕ ЧЕРЕЗ BWebhookConstructor
 */
class BCrmCompanyRepository
{
    /**
     * @param string $groupId
     * @return int|null
     */
    public static function getCompanyIdByGroupId(string $groupId): ?int
    {
        return BCrmCompany::query()
            ->select('ID')
            ->where(
                'TITLE',
                '=',
                BSonetGroup::query()
                    ->select('NAME')
                    ->where('ID', '=', $groupId)
                    ->value('NAME')
            )
            ->value('ID');
    }

    public static function autocomplete(string $term): Collection
    {
        return BCrmCompany::query()
            ->select(
                'ID as value',
                'TITLE as label'
            )
            ->where('TITLE', 'LIKE', '%' . $term . '%')
            ->limit(10)
            ->get();
    }
}
