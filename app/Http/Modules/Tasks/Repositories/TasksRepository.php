<?php

namespace App\Http\Modules\Tasks\Repositories;

use App\Models\Tasks\Task;
use Illuminate\Contracts\Pagination\Paginator;

class TasksRepository
{
    public static function getListByCreatorId(
        int $creatorId,
        ?string $search,
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
            ->when(! empty($search), function ($query) use ($search) {
                $like = '%'.addcslashes($search, '%_\\').'%';

                return $query->where(function ($q) use ($like) {
                    $q->where('id', 'LIKE', $like)
                        ->orWhere('b_task_id', 'LIKE', $like)
                        ->orWhereHas('bTask', function ($q2) use ($like) {
                            $q2->where('TITLE', 'LIKE', $like);
                        });
                });
            })
            ->when(! empty($problemTypeId), function ($query) use ($problemTypeId) {
                return $query->where('problem_type_id', '=', $problemTypeId);
            })
            ->when(! empty($personPhone), function ($query) use ($personPhone) {
                return $query->where('person_phone', '=', $personPhone);
            })
            ->whereHas('bTask', function ($q) use ($statusId, $priorityId, $dateStart, $dateEnd) {
                return $q
                    ->when(! empty($statusId), function ($q) use ($statusId) {
                        return $q->where('STATUS', '=', $statusId);
                    })
                    ->when(! empty($priorityId), function ($q) use ($priorityId) {
                        return $q->where('PRIORITY', '=', $priorityId);
                    })
                    ->when(! empty($dateStart), function ($q) use ($dateStart) {
                        return $q->whereDate('CREATED_DATE', '>=', $dateStart);
                    })
                    ->when(! empty($dateEnd), function ($q) use ($dateEnd) {
                        return $q->whereDate('CREATED_DATE', '<=', $dateEnd);
                    });
            })
            ->orderBy('id', 'desc')
            ->simplePaginate(perPage: $perPage);
    }

    public static function getAllWithFilter(
        ?string $search,
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
            ->when(! empty($search), function ($query) use ($search) {
                $like = '%'.addcslashes($search, '%_\\').'%';

                return $query->where(function ($q) use ($like) {
                    $q->where('id', 'LIKE', $like)
                        ->orWhere('b_task_id', 'LIKE', $like)
                        ->orWhereHas('bTask', function ($q2) use ($like) {
                            $q2->where('TITLE', 'LIKE', $like);
                        });
                });
            })
            ->when(! empty($problemTypeId), function ($query) use ($problemTypeId) {
                return $query->where('problem_type_id', '=', $problemTypeId);
            })
            ->when(! empty($personPhone), function ($query) use ($personPhone) {
                return $query->where('person_phone', '=', $personPhone);
            })
            ->whereHas('bTask', function ($q) use ($statusId, $priorityId, $dateStart, $dateEnd) {
                return $q
                    ->when(! empty($statusId), function ($q) use ($statusId) {
                        return $q->where('STATUS', '=', $statusId);
                    })
                    ->when(! empty($priorityId), function ($q) use ($priorityId) {
                        return $q->where('PRIORITY', '=', $priorityId);
                    })
                    ->when(! empty($dateStart), function ($q) use ($dateStart) {
                        return $q->whereDate('CREATED_DATE', '>=', $dateStart);
                    })
                    ->when(! empty($dateEnd), function ($q) use ($dateEnd) {
                        return $q->whereDate('CREATED_DATE', '<=', $dateEnd);
                    });
            })
            ->orderBy('id', 'desc')
            ->simplePaginate(perPage: $perPage);
    }

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
                'creator',
                'messages.user',
            ])
            ->where('id', '=', $taskId)
            ->first();
    }

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
                'creator',
                'messages.user',
            ])
            ->where('id', '=', $taskId)
            ->where('user_creator_id', '=', $creatorId)
            ->first();
    }

    /**
     * Лёгкая выборка обращения без связей — для отправки сообщения.
     */
    public static function getModelById(int $taskId): ?Task
    {
        return Task::query()
            ->select('id', 'b_task_id', 'user_creator_id')
            ->where('id', '=', $taskId)
            ->first();
    }

    /**
     * Лёгкая выборка обращения, доступного только его создателю.
     */
    public static function getModelByIdForCreator(int $taskId, int $creatorId): ?Task
    {
        return Task::query()
            ->select('id', 'b_task_id', 'user_creator_id')
            ->where('id', '=', $taskId)
            ->where('user_creator_id', '=', $creatorId)
            ->first();
    }

    public static function store(array $fields): int
    {
        return Task::query()->insertGetId($fields);
    }
}
