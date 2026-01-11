<?php

namespace App\Http\Modules\Tasks\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Tasks\Services\TasksStatusesService;
use Illuminate\Http\JsonResponse;

/**
 * @group Tasks|Обращения
 * @subgroup Statuses|Статусы
 * @subgroupDescription Статусы обращений
 */
class TasksStatusesController extends Controller
{
    /**
     * Получить статусы задач
     *
     * @response {
     *  "data": [
     *      {
     *          "id": 1,
     *          "name": "Новая задача",
     *          "variant": "default"
     *      }
     * ]
     * }
     *
     * @param TasksStatusesService $service
     * @return JsonResponse
     */
    public function getAll(
        TasksStatusesService $service
    ): JsonResponse {
        return response()->json($service->getAll());
    }
}
