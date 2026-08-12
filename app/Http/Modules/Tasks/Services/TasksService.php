<?php

namespace App\Http\Modules\Tasks\Services;

use App\Http\Modules\Bitrix\Repositories\BDiskAttachedObjectsRepository;
use App\Http\Modules\Bitrix\Services\BCrmContactService;
use App\Http\Modules\Bitrix\Services\BDiskService;
use App\Http\Modules\Bitrix\Services\BTasksService;
use App\Http\Modules\Bitrix\Services\VacationScheduleService;
use App\Http\Modules\Notifications\Services\TelegramNotifyService;
use App\Http\Modules\Tasks\Repositories\TaskMessagesRepository;
use App\Http\Modules\Tasks\Repositories\TasksProblemTypesRepository;
use App\Http\Modules\Tasks\Repositories\TasksRepository;
use App\Http\Modules\Tasks\Requests\TaskStoreRequest;
use App\Http\Modules\Tasks\Resources\TaskMessageResource;
use App\Http\Modules\Tasks\Resources\TaskResource;
use App\Http\Modules\Tasks\Resources\TasksPaginateCollection;
use App\Models\Tasks\Task;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TasksService
{
    public function getByCreatorId(
        Request $request,
        int $creatorId
    ): TasksPaginateCollection {
        $tasksList = TasksRepository::getListByCreatorId(
            creatorId: $creatorId,
            search: $request->query('search'),
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

    public function getAllWithFilter(
        Request $request
    ): TasksPaginateCollection {
        $tasksList = TasksRepository::getAllWithFilter(
            search: $request->query('search'),
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

    public function getByIdOnlyForCreator(int $taskId, int $creatorId): ?TaskResource
    {
        $task = TasksRepository::getByIdOnlyForCreator($taskId, $creatorId);
        if (! empty($task)) {
            return $this->makeDetailResource($task);
        }

        return null;
    }

    public function getById(int $taskId): ?TaskResource
    {
        $task = TasksRepository::getById($taskId);
        if (! empty($task)) {
            return $this->makeDetailResource($task);
        }

        return null;
    }

    /**
     * @throws ConnectionException
     */
    public function store(TaskStoreRequest $request): TaskResource
    {
        $bTaskService = new BTasksService;
        $bDiskService = new BDiskService;
        $bCrmContactService = new BCrmContactService;
        $vacationScheduleService = new VacationScheduleService;

        // Получаем привязанные данные к типу проблемы
        $problemType = TasksProblemTypesRepository::getById(
            id: $request->post('problem_type_id'),
            withProblems: true,
            withPerformers: true,
            withWatchers: true
        );
        // Определяем выбранную проблему
        $taskProblem = null;
        foreach ($problemType->problems as $problem) {
            if ($problem->id === (int) $request->post('problem_id')) {
                $taskProblem = $problem;
                break;
            }
        }

        // Получаем пользователей в отпуске
        $userIdsOnVacation = $vacationScheduleService->getAllUserIdsOnVacation();
        // Получаем исполнителей проблемы и убираем тех, кто в отпуске
        $responsibleIds = $problemType->performers->pluck('b_user_id')->toArray();
        if (! empty($responsibleIds)) {
            $responsibleIds = array_diff($responsibleIds, $userIdsOnVacation);
        }
        // Получаем наблюдателей проблемы и убираем тех, кто в отпуске
        $watcherIds = $problemType->watchers->pluck('b_user_id')->toArray();
        if (! empty($watcherIds)) {
            $watcherIds = array_diff($watcherIds, $userIdsOnVacation);
        }
        // Создаем контакт в битре, если его не было или получаем id
        $contactId = $bCrmContactService->getContactIdOrStore(
            $request->post('person'),
            $request->post('person_phone')
        );
        $bTaskId = $bTaskService->store($request, $responsibleIds, $watcherIds);

        try {
            (new TelegramNotifyService)->notifyTaskCreated($request->post('name'), $bTaskId);
        } catch (\Throwable $exception) {
            report($exception);
        }

        $bTaskService->addFirstComment(
            $bTaskId,
            $request,
            $problemType->name,
            $taskProblem?->name ?? null,
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

    /**
     * Сохраняет сообщение переписки локально и отправляет его копией
     * в чат (комментарий) задачи Битрикса. Чат из Битрикса не читаем.
     *
     * Вложения не дублируем у себя: загружаем их в Битрикс-Диск, прикрепляем к комментарию,
     * а локально храним только ID файлов — сами вложения резолвим из Битрикса по запросу.
     *
     * @param  array<int, \Illuminate\Http\UploadedFile>  $files
     *
     * Возвращает null, если обращение не найдено или недоступно пользователю.
     */
    public function addMessage(int $taskId, string $text, array $files = []): ?TaskMessageResource
    {
        $user = Auth::user();
        $task = $user->can('tasks_all_users')
            ? TasksRepository::getModelById($taskId)
            : TasksRepository::getModelByIdForCreator($taskId, $user->getAuthIdentifier());

        if (empty($task)) {
            return null;
        }

        // Загружаем вложения в Битрикс-Диск. Падение загрузки не должно терять сообщение —
        // в этом случае просто уходит текст без файлов.
        $diskFileIds = [];
        if (! empty($files)) {
            try {
                $diskFileIds = (new BDiskService)->store($files, $task->b_task_id);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        // Отправляем в задачу Битрикса от имени бота с префиксом имени клиента.
        // Падение Битрикса не должно терять сообщение — оно всё равно сохраняется локально.
        $bCommentId = null;
        try {
            // $text приходит готовым HTML из Tiptap-редактора — не экранируем и не nl2br'им,
            // иначе в Битрикс уезжали бы литералы <br /> и экранированные теги
            $bMessage = '<p>Сообщение от '.e($user->name).':</p>'.$text;
            $bCommentId = (new BTasksService)->addComment($task->b_task_id, $bMessage, $diskFileIds);
        } catch (\Throwable $exception) {
            report($exception);
        }

        $now = Carbon::now()->toDateTimeString();
        $messageId = TaskMessagesRepository::store([
            'task_id' => $task->id,
            'user_id' => $user->getAuthIdentifier(),
            'text' => $text,
            'b_comment_id' => $bCommentId,
            // Сырая вставка минует касты модели — кодируем массив ID в JSON руками
            'b_disk_file_ids' => ! empty($diskFileIds) ? json_encode($diskFileIds) : null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return (new TaskMessageResource(TaskMessagesRepository::getById($messageId)))
            ->withFiles($this->resolveDiskFiles($diskFileIds));
    }

    /**
     * Содержимое прикреплённого файла Диска для стрима в браузер (web-роут tasks.disk-file).
     *
     * @return array{name: string, content_type: string, body: string}|null
     */
    public function getDiskFileContent(int $fileId): ?array
    {
        try {
            return (new BDiskService)->downloadAttachedObject($fileId);
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
    }

    /**
     * Собирает TaskResource с уже разрешёнными вложениями Диска: имена и признак картинки
     * резолвим на бэке один раз, чтобы фронт только отрисовывал — без парсинга и доп. запросов.
     *
     * Источник файлов описания и результатов — b_disk_attached_object (привязки файлов к
     * сущностям по ENTITY_TYPE + ENTITY_ID), а не разбор маркеров [DISK FILE ID=…] в тексте:
     * текст мог быть отредактирован, а файлы остаются привязанными к сущности. Вложения
     * переписки — из task_messages.b_disk_file_ids (как раньше).
     */
    private function makeDetailResource(Task $task): TaskResource
    {
        // ID вложений описания — по задаче (b_tasks.ID)
        $descriptionIds = [];
        $bTaskId = $task->bTask?->ID;
        if ($bTaskId !== null) {
            $descriptionIds = BDiskAttachedObjectsRepository::objectIdsForEntity(
                BDiskAttachedObjectsRepository::ENTITY_TYPE_TASK,
                $bTaskId
            );
        }

        // ID вложений результатов — по каждому результату (b_tasks_result.ID)
        $resultIdsByResultId = [];
        $resultIds = [];
        foreach ($task->bResult as $result) {
            $ids = BDiskAttachedObjectsRepository::objectIdsForEntity(
                BDiskAttachedObjectsRepository::ENTITY_TYPE_TASK_RESULT,
                $result->ID
            );
            $resultIdsByResultId[$result->ID] = $ids;
            $resultIds = array_merge($resultIds, $ids);
        }

        // Вложения переписки: ID хранятся в task_messages.b_disk_file_ids (каст в массив)
        $messageIds = [];
        foreach ($task->messages as $message) {
            if (! empty($message->b_disk_file_ids)) {
                $messageIds = array_merge($messageIds, array_map('intval', $message->b_disk_file_ids));
            }
        }

        $resolved = $this->resolveDiskFiles(array_merge($descriptionIds, $resultIds, $messageIds));

        // Готовые наборы файлов (только успешно разрешённые) — ресурс отдаёт их как есть
        $pick = fn (array $ids) => array_values(array_filter(
            array_map(fn ($id) => $resolved[$id] ?? null, $ids),
            fn ($file) => $file !== null
        ));

        $resultFiles = [];
        foreach ($resultIdsByResultId as $resultId => $ids) {
            $resultFiles[$resultId] = $pick($ids);
        }

        return (new TaskResource($task))
            ->withDiskFiles($resolved)
            ->withDescriptionFiles($pick($descriptionIds))
            ->withResultFiles($resultFiles);
    }

    /**
     * Разрешает ID вложений в объекты {id, name, is_image, url} через Битрикс-Диск.
     * Падение по конкретному файлу не роняет обращение — просто пропускаем его.
     *
     * @param  int[]  $ids
     * @return array<int, object>
     */
    private function resolveDiskFiles(array $ids): array
    {
        $ids = array_values(array_unique($ids));
        if (empty($ids)) {
            return [];
        }

        $bDiskService = new BDiskService;
        $resolved = [];
        foreach ($ids as $id) {
            try {
                $object = $bDiskService->getAttachedObject($id);
            } catch (\Throwable $exception) {
                report($exception);

                continue;
            }

            if ($object === null) {
                continue;
            }

            $resolved[$id] = (object) [
                'id' => $id,
                'name' => $object['name'],
                'is_image' => $this->isImageName($object['name']),
                'url' => route('tasks.disk-file', ['fileId' => $id]),
            ];
        }

        return $resolved;
    }

    private function isImageName(string $name): bool
    {
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'], true);
    }
}
