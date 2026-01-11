<?php

namespace App\Http\Modules\Bitrix\Services;

use App\Http\Modules\Bitrix\Repositories\VacationScheduleRepository;
use Carbon\Carbon;

class VacationScheduleService
{
    /**
     * Убираем из массива пользователей, тех кто в отпуске
     * @param array $userIds
     * @return array
     */
    public function diffUsersOnVacation(array $userIds): array
    {
        return array_diff(
            $userIds,
            VacationScheduleRepository::getUserIdsOnVacation($userIds, Carbon::now()->toDateString())->toArray()
        );
    }

    /**
     * Получить всех пользователей, находящихся в отпуске
     * @return array
     */
    public function getAllUserIdsOnVacation(): array
    {
        return VacationScheduleRepository::getAllUserIdsOnVacation(Carbon::now()->toDateString())->toArray();
    }
}
