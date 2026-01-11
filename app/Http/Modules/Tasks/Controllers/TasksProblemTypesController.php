<?php

namespace App\Http\Modules\Tasks\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Tasks\Requests\TasksProblemTypesGetAllRequest;
use App\Http\Modules\Tasks\Services\TasksProblemTypesService;
use Illuminate\Http\JsonResponse;

/**
 * @group Tasks|Обращения
 * @subgroup Types of problems|Типы проблем
 * @subgroupDescription Типы проблем обращений
 */
class TasksProblemTypesController extends Controller
{
    /**
     * Получить типы проблем задач
     *
     * @response {
     *    "data": [
     *        {
     *            "id": 1,
     *            "name": "Проблема в 1С БУХ/ЗУП/УТ",
     *            "problems": [
     *                  {
     *                      "id": 1,
     *                      "name": "Проблема в 1С БУХ/ЗУП/УТ",
     *                      "problem_type_id": 1
     *                  }
     *            ]
     *        }
     *   ]
     *   }
     * @param TasksProblemTypesGetAllRequest $request
     * @param TasksProblemTypesService $service
     * @return JsonResponse
     */
    public function getAll(
        TasksProblemTypesGetAllRequest $request,
        TasksProblemTypesService $service
    ): JsonResponse {
        return response()->json($service->getAll($request->filled('with_problems')));
    }
}
