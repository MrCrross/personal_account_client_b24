<?php

namespace App\Http\Modules\Bitrix\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Bitrix\Services\BCrmCompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Bitrix24|Битрикс 24
 *
 * Маршруты к сущностям Битрикс 24
 *
 * @subgroup Companies|Компании
 * @subgroupDescription Маршруты к b_crm_company
 */
class BCrmCompanyController extends Controller
{
    /**
     * Автокомплит для компаний из B24
     *
     * @response {
     *      "data": [
     *          {
     *              "value":1,
     *              "label":"Значение"
     *          }
     *      ]
     *  }
     *
     * @param Request $request
     * @param BCrmCompanyService $companyService
     * @return JsonResponse
     */
    public function autocomplete(
        Request $request,
        BCrmCompanyService $companyService
    ): JsonResponse {
        return response()->json(
            $companyService->autocomplete($request->query('term',''))
        );
    }
}
