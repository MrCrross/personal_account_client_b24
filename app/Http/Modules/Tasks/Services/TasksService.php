<?php

namespace App\Http\Modules\Tasks\Services;

use App\Http\Modules\Bitrix\Services\BCrmContactService;
use App\Http\Modules\Bitrix\Services\BDiskService;
use App\Http\Modules\Bitrix\Services\BTasksService;
use App\Http\Modules\Bitrix\Services\VacationScheduleService;
use App\Http\Modules\Tasks\Repositories\TasksProblemTypesRepository;
use App\Http\Modules\Tasks\Repositories\TasksRepository;
use App\Http\Modules\Tasks\Requests\TaskStoreRequest;
use App\Http\Modules\Tasks\Resources\TaskResource;
use App\Http\Modules\Tasks\Resources\TasksPaginateCollection;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TasksService
{
    /**
     * @param Request $request
     * @param int $creatorId
     * @return TasksPaginateCollection
     */
    public function getByCreatorId(
        Request $request,
        int $creatorId
    ): TasksPaginateCollection {
        $tasksList = TasksRepository::getListByCreatorId(
            creatorId: $creatorId,
            problemTypeId: $request->query('problem_type_id'),
            statusId: $request->query('status_id'),
            priorityId: $request->query('priority_id'),
            personPhone: $request->query('person_phone'),
            dateStart: $request->query('date_start'),
            dateEnd: $request->query('date_end'),
            perPage: $request->query('per_page', 15)
        );

        return new TasksPaginateCollection($tasksList);
    }

    /**
     * @param Request $request
     * @return TasksPaginateCollection
     */
    public function getAllWithFilter(
        Request $request
    ): TasksPaginateCollection {
        $tasksList = TasksRepository::getAllWithFilter(
            problemTypeId: $request->query('problem_type_id'),
            statusId: $request->query('status_id'),
            priorityId: $request->query('priority_id'),
            personPhone: $request->query('person_phone'),
            dateStart: $request->query('date_start'),
            dateEnd: $request->query('date_end'),
            perPage: $request->query('per_page', 15)
        );

        return new TasksPaginateCollection($tasksList);
    }

    /**
     * @param int $taskId
     * @param int $creatorId
     * @return TaskResource|null
     */
    public function getByIdOnlyForCreator(int $taskId, int $creatorId): TaskResource|null
    {
        $task = TasksRepository::getByIdOnlyForCreator($taskId, $creatorId);
        if (!empty($task)) {
            return new TaskResource($task);
        }

        return null;
    }

    /**
     * @param int $taskId
     * @return TaskResource|null
     */
    public function getById(int $taskId): TaskResource|null
    {
        $task = TasksRepository::getById($taskId);
        if (!empty($task)) {
            return new TaskResource($task);
        }

        return null;
    }

    /**
     * @param TaskStoreRequest $request
     * @return TaskResource
     * @throws ConnectionException
     */
    public function store(TaskStoreRequest $request): TaskResource
    {
        $bTaskService = new BTasksService();
        $bDiskService = new BDiskService();
        $bCrmContactService = new BCrmContactService();
        $vacationScheduleService = new VacationScheduleService();

        // Получаем привязанные данные к типу проблемы
        $problemType = TasksProblemTypesRepository::getById(
            id: $request->post('problem_type_id'),
            withProblems: true,
            withPerformers: true,
            withWatchers: true
        );
        // Определяем выбранную проблему
        $taskProblem = [];
        foreach ($problemType->problems as $problem) {
            if ($problem->id === (int)$request->post('problem_id')) {
                $taskProblem = $problem;
                break;
            }
        }

        //Получаем пользователей в отпуске
        $userIdsOnVacation = $vacationScheduleService->getAllUserIdsOnVacation();
        // Получаем исполнителей проблемы и убираем тех, кто в отпуске
        $responsibleIds = $problemType->performers->pluck('b_user_id')->toArray();
        if (!empty($responsibleIds)) {
            $responsibleIds = array_diff($responsibleIds, $userIdsOnVacation);
        }
        // Получаем наблюдателей проблемы и убираем тех, кто в отпуске
        $watcherIds = $problemType->watchers->pluck('b_user_id')->toArray();
        if (!empty($watcherIds)) {
            $watcherIds = array_diff($watcherIds, $userIdsOnVacation);
        }
        // Создаем контакт в битре, если его не было или получаем id
        $contactId = $bCrmContactService->getContactIdOrStore(
            $request->post('person'),
            $request->post('person_phone')
        );
        $bTaskId = $bTaskService->store($request, $responsibleIds, $watcherIds);
        $bTaskService->addFirstComment(
            $bTaskId,
            $request,
            $problemType->name,
            $problem?->name ?? null,
            $contactId
        );
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $uploadFilesIds = $bDiskService->store($files, $bTaskId);
            $bTaskService->uploadFiles($bTaskId, $uploadFilesIds);
        }

        $taskFields = [
            'person' => $request->post('person'),
            'person_phone' => $request->post('person_phone'),
            'b_task_id' => $bTaskId,
            'user_creator_id' => Auth::user()->getAuthIdentifier(),
            'problem_type_id' => $problemType->id,
            'problem_id' => $taskProblem?->id ?? null,
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
        $taskId = TasksRepository::store($taskFields);

        return new TaskResource(TasksRepository::getById($taskId));
    }
}
