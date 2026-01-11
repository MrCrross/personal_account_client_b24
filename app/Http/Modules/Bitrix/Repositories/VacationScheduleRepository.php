<?php

namespace App\Http\Modules\Bitrix\Repositories;

use App\Models\Bitrix24\Iblock\BIblockElement;
use App\Models\Bitrix24\Iblock\BIblockElementProperty;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * НЕ ИСПОЛЬЗОВАТЬ ДЛЯ ВСТАВКИ ДАННЫХ
 * ЛЮБОЕ НЕ ПОЛУЧЕНИЕ ЧЕРЕЗ BWebhookConstructor
 */
class VacationScheduleRepository
{
    private static int $vacationScheduleIBlockId = 151; // id блока Графика Отпусков
    private static int $workerIBlockPropertyId = 409; // id свойства Сотрудник
    private static int $dateStartVacationIBlockPropertyId = 410; // id свойства Начало отпуска
    private static int $dateEndVacationIBlockPropertyId = 411; // id свойства Окончание отпуска

    /**
     * Получаем пользователей, которые сейчас в отпуске
     *
     * @param array $userIds
     * @param string $vacationDate
     * @return Collection
     */
    public static function getUserIdsOnVacation(array $userIds, string $vacationDate): Collection
    {
        $iBElementTable = (new BIblockElement())->getTable();
        $iBElementPropertyTable = (new BIblockElementProperty())->getTable();

        return DB::table($iBElementPropertyTable . ' as ep_worker')
            ->select('ep_worker.VALUE')
            ->distinct()
            ->join($iBElementTable . ' as element', 'element.ID', '=', 'ep_worker.IBLOCK_ELEMENT_ID')
            ->join($iBElementPropertyTable . ' as ep_date_start', 'element.ID', '=', 'ep_date_start.IBLOCK_ELEMENT_ID')
            ->join($iBElementPropertyTable . ' as ep_date_end', 'element.ID', '=', 'ep_date_end.IBLOCK_ELEMENT_ID')
            ->where('element.IBLOCK_ID', '=', self::$vacationScheduleIBlockId)
            ->where('ep_worker.IBLOCK_PROPERTY_ID', '=', self::$workerIBlockPropertyId)
            ->whereIn('ep_worker.VALUE', $userIds)
            ->where('ep_date_start.IBLOCK_PROPERTY_ID', '=', self::$dateStartVacationIBlockPropertyId)
            ->where('ep_date_start.VALUE', '<', $vacationDate)
            ->where('ep_date_start.IBLOCK_PROPERTY_ID', '=', self::$dateEndVacationIBlockPropertyId)
            ->where('ep_date_start.VALUE', '>', $vacationDate)
            ->pluck('VALUE');
    }

    /**
     * Получаем пользователей, которые сейчас в отпуске
     *
     * @param string $vacationDate
     * @return Collection
     */
    public static function getAllUserIdsOnVacation(string $vacationDate): Collection
    {
        $iBElementTable = (new BIblockElement())->getTable();
        $iBElementPropertyTable = (new BIblockElementProperty())->getTable();

        return DB::table($iBElementPropertyTable . ' as ep_worker')
            ->select('ep_worker.VALUE')
            ->distinct()
            ->join($iBElementTable . ' as element', 'element.ID', '=', 'ep_worker.IBLOCK_ELEMENT_ID')
            ->join($iBElementPropertyTable . ' as ep_date_start', 'element.ID', '=', 'ep_date_start.IBLOCK_ELEMENT_ID')
            ->join($iBElementPropertyTable . ' as ep_date_end', 'element.ID', '=', 'ep_date_end.IBLOCK_ELEMENT_ID')
            ->where('element.IBLOCK_ID', '=', self::$vacationScheduleIBlockId)
            ->where('ep_worker.IBLOCK_PROPERTY_ID', '=', self::$workerIBlockPropertyId)
            ->where('ep_date_start.IBLOCK_PROPERTY_ID', '=', self::$dateStartVacationIBlockPropertyId)
            ->where('ep_date_start.VALUE', '<', $vacationDate)
            ->where('ep_date_start.IBLOCK_PROPERTY_ID', '=', self::$dateEndVacationIBlockPropertyId)
            ->where('ep_date_start.VALUE', '>', $vacationDate)
            ->pluck('VALUE');
    }
}
