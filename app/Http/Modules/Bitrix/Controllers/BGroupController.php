<?php

namespace App\Http\Modules\Bitrix\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Bitrix\Services\BGroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Bitrix24|Битрикс 24
 * @subgroup Groups|Группы
 * @subgroupDescription Маршруты к b_sonet_group
 */
class BGroupController extends Controller
{
    /**
     * Автокомплит для групп из B24
     *
     * @response {
     *     "data": [
     *         {
     *             "value":1,
     *             "label":"Значение"
     *         }
     *     ]
     * }
     *
     * @param Request $request
     * @param BGroupService $groupService
     * @return JsonResponse
     */
    public function autocomplete(
        Request $request,
        BGroupService $groupService
    ): JsonResponse {
        return response()->json(
            $groupService->autocomplete($request->query('term',''))
        );
    }
}
