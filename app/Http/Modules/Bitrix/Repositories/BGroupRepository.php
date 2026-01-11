<?php

namespace App\Http\Modules\Bitrix\Repositories;

use App\Models\Bitrix24\BSonetGroup;
use Illuminate\Database\Eloquent\Collection;

/**
 * НЕ ИСПОЛЬЗОВАТЬ ДЛЯ ВСТАВКИ ДАННЫХ
 * ЛЮБОЕ НЕ ПОЛУЧЕНИЕ ЧЕРЕЗ BGroupService
 */
class BGroupRepository
{
    /**
     * @return Collection
     */
    public static function getAll(): Collection
    {
        return BSonetGroup::query()
            ->select(
                'ID',
                'NAME'
            )
            ->where('ACTIVE', '=', 'Y')
            ->get();
    }

    /**
     * @param string $term
     * @return Collection
     */
    public static function autocomplete(string $term): Collection
    {
        return BSonetGroup::query()
            ->select(
                'ID as value',
                'NAME as label'
            )
            ->where('NAME', 'LIKE', '%' . $term . '%')
            ->where('ACTIVE', '=', 'Y')
            ->limit(10)
            ->get();
    }
}
