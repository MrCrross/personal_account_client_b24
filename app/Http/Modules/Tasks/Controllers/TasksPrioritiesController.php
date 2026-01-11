<?php

namespace App\Http\Modules\Tasks\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Tasks\Services\TasksPrioritiesService;
use Illuminate\Http\JsonResponse;

/**
 * @group Tasks|Обращения
 * @subgroup Priorities|Приоритеты
 * @subgroupDescription Приоритеты обращений
 */
class TasksPrioritiesController extends Controller
{
    /**
     * Получить приоритеты задач
     *
     * @response {
     *   "data": [
     *       {
     *           "id": 0,
     *           "name": "Низкий",
     *       }
     *  ]
     *  }
     * @param TasksPrioritiesService $service
     * @return JsonResponse
     */
    public function getAll(
        TasksPrioritiesService $service
    ): JsonResponse {
        return response()->json($service->getAll());
    }
}
