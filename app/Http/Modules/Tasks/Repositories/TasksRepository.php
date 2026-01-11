<?php

namespace App\Http\Modules\Tasks\Repositories;

use App\Models\Tasks\Task;
use Illuminate\Contracts\Pagination\Paginator;

class TasksRepository
{
    /**
     * @param int $creatorId
     * @param int|null $problemTypeId
     * @param int|null $statusId
     * @param int|null $priorityId
     * @param string|null $personPhone
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @param int $perPage
     * @return Paginator
     */
    public static function getListByCreatorId(
        int $creatorId,
        ?int $problemTypeId,
        ?int $statusId,
        ?int $priorityId,
        ?string $personPhone,
        ?string $dateStart,
        ?string $dateEnd,
        int $perPage = 15
    ): Paginator {
        return Task::query()
            ->select(
                'id',
                'person',
                'person_phone',
                'b_task_id',
                'problem_type_id',
                'user_creator_id'
            )
            ->with([
                'bTask.group',
                'bTask.user',
                'bTask.taskStatus',
                'bTask.taskPriority',
                'bResult.creator',
                'problemType',
                'creator',
            ])
            ->where('user_creator_id', '=', $creatorId)
            ->when(!empty($problemTypeId), function ($query) use ($problemTypeId) {
                return $query->where('problem_type_id', '=', $problemTypeId);
            })
            ->when(!empty($personPhone), function ($query) use ($personPhone) {
                return $query->where('person_phone', '=', $personPhone);
            })
            ->whereHas('bTask', function ($query) use ($statusId, $priorityId, $dateStart, $dateEnd) {
                return $query
                    ->when(!empty($statusId), function ($q) use ($statusId) {
                        return $q->where('STATUS', '=', $statusId);
                    })
                    ->when(!empty($priorityId), function ($q) use ($priorityId) {
                        return $q->where('PRIORITY', '=', $priorityId);
                    })
                    ->when(!empty($dateStart), function ($q) use ($dateStart) {
                        return $q->where('CREATED_DATE', '>=', $dateStart);
                    })
                    ->when(!empty($dateEnd), function ($q) use ($dateEnd) {
                        return $q->where('CREATED_DATE', '<=', $dateEnd);
                    });
            })
            ->orderBy('id', 'desc')
            ->simplePaginate(perPage: $perPage);
    }

    /**
     * @param int|null $problemTypeId
     * @param int|null $statusId
     * @param int|null $priorityId
     * @param string|null $personPhone
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @param int $perPage
     * @return Paginator
     */
    public static function getAllWithFilter(
        ?int $problemTypeId,
        ?int $statusId,
        ?int $priorityId,
        ?string $personPhone,
        ?string $dateStart,
        ?string $dateEnd,
        int $perPage = 15
    ): Paginator {
        return Task::query()
            ->select(
                'id',
                'person',
                'person_phone',
                'b_task_id',
                'problem_type_id',
                'user_creator_id'
            )
            ->with([
                'bTask.group',
                'bTask.user',
                'bTask.taskStatus',
                'bTask.taskPriority',
                'bResult.creator',
                'problemType',
                'creator'
            ])
            ->when(!empty($problemTypeId), function ($query) use ($problemTypeId) {
                return $query->where('problem_type_id', '=', $problemTypeId);
            })
            ->when(!empty($personPhone), function ($query) use ($personPhone) {
                return $query->where('person_phone', '=', $personPhone);
            })
            ->whereHas('bTask', function ($query) use ($statusId, $priorityId, $dateStart, $dateEnd) {
                return $query
                    ->when(!empty($statusId), function ($q) use ($statusId) {
                        return $q->where('STATUS', '=', $statusId);
                    })
                    ->when(!empty($priorityId), function ($q) use ($priorityId) {
                        return $q->where('PRIORITY', '=', $priorityId);
                    })
                    ->when(!empty($dateStart), function ($q) use ($dateStart) {
                        return $q->where('CREATED_DATE', '>=', $dateStart);
                    })
                    ->when(!empty($dateEnd), function ($q) use ($dateEnd) {
                        return $q->where('CREATED_DATE', '<=', $dateEnd);
                    });
            })
            ->orderBy('id', 'desc')
            ->simplePaginate(perPage: $perPage);
    }

    /**
     * @param int $taskId
     * @return Task|null
     */
    public static function getById(int $taskId): ?Task
    {
        return Task::query()
            ->select(
                'id',
                'person',
                'person_phone',
                'b_task_id',
                'problem_type_id',
                'user_creator_id'
            )
            ->with([
                'bTask.group',
                'bTask.user',
                'bTask.taskStatus',
                'bResult.creator',
                'problemType',
                'creator'
            ])
            ->where('id', '=', $taskId)
            ->first();
    }

    /**
     * @param int $taskId
     * @param int $creatorId
     * @return Task|null
     */
    public static function getByIdOnlyForCreator(int $taskId, int $creatorId): ?Task
    {
        return Task::query()
            ->select(
                'id',
                'person',
                'person_phone',
                'b_task_id',
                'problem_type_id',
                'user_creator_id'
            )
            ->with([
                'bTask.group',
                'bTask.user',
                'bTask.taskStatus',
                'bResult.creator',
                'problemType',
                'creator'
            ])
            ->where('id', '=', $taskId)
            ->where('user_creator_id', '=', $creatorId)
            ->first();
    }

    /**
     * @param array $fields
     * @return int
     */
    public static function store(array $fields): int
    {
        return Task::query()->insertGetId($fields);
    }
}
