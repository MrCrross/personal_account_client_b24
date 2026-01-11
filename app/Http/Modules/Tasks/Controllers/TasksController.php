<?php

namespace App\Http\Modules\Tasks\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Tasks\Requests\TasksListFilterRequest;
use App\Http\Modules\Tasks\Requests\TaskStoreRequest;
use App\Http\Modules\Tasks\Services\TasksService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Tasks|Обращения
 *
 * Обращения пользователей с проблемами, по которым создаются задачи в Битрикс 24
 */
class TasksController extends Controller
{
    /**
     * Получить данные задачи
     *
     * @response {
     *  "id": 1,
     *  "person": "Иванов Иван Иваныч",
     *  "person_phone": "89641234567",
     *  "b_id": 100,
     *  "title": "Тестовое обращение",
     *  "description": "Тестовое описание",
     *  "status": {
     *      "id": 1,
     *      "name": "Новая задача",
     *      "variant": "default"
     *  },
     * "priority": {
     *     "id":1,
     *     "name": "Средний"
     * },
     * "group": {
     *     "id": 12,
     *     "name": "Техническая поддержка"
     * },
     * "responsible": {
     *     "id": 1,
     *     "name": "Иван",
     *     "last_name": "Иванов",
     * },
     * "changed_date": "2025-09-10 12:00:00",
     * "closed_date": null,
     * "date_start": "2025-09-10 10:00:00",
     * "deadline": null,
     * "start_date_plan": null,
     * "end_date_plan": null,
     * "duration_plan": null,
     * "duration_fact": null,
     * "problem_type": {
     *     "id": 1,
     *     "name": "Проблема в 1С БУХ/ЗУП/УТ"
     * },
     * "results": null,
     * "creator": {
     *     "id": 1,
     *     "name": "Тестовый пользователь"
     * }
     * }
     * @param int $id
     * @param TasksService $tasksService
     * @return JsonResponse
     */
    public function getById(
        int $id,
        TasksService $tasksService
    ): JsonResponse {
        if (Auth::user()->can('tasks_all_users')) {
            $task = $tasksService->getById($id);
        } else {
            $authUserId = Auth::user()->getAuthIdentifier();
            $task = $tasksService->getByIdOnlyForCreator($id, $authUserId);
        }

        return response()->json($task);
    }

    /**
     * Получить список задач пользователя
     *
     * Возвращается массив объектов /api/tasks/{id}
     *
     * @param TasksListFilterRequest $request
     * @param TasksService $tasksService
     * @return JsonResponse
     */
    public function getList(
        TasksListFilterRequest $request,
        TasksService $tasksService
    ): JsonResponse {
        if (Auth::user()->can('tasks_all_users')) {
            $tasks = $tasksService->getAllWithFilter($request);
        } else {
            $authUserId = Auth::user()->getAuthIdentifier();
            $tasks = $tasksService->getByCreatorId($request, $authUserId);
        }

        return response()->json($tasks);
    }

    /**
     * Добавить обращение
     *
     * Возвращается объект /api/tasks/{id}
     *
     * Store a newly created resource in storage.
     */
    public function store(
        TaskStoreRequest $request,
        TasksService $tasksService
    ): JsonResponse {
        return response()->json($tasksService->store($request));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
