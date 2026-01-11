<?php

namespace App\Http\Modules\Bitrix\Services;

use App\Http\Modules\Tasks\Enums\TaskStatusesEnum;
use App\Http\Wrappers\BWebHookConstructor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BTasksService extends BWebHookConstructor
{
    private static array $DEFAULT_AUDITORS = [1]; // Никита Залесский
    private static int $RESPONSIBLE_BOT = 312; // БОТ ФЛЕШ-ФОРМАТ

    public function store(
        Request $request,
        array $responsibleIds,
        array $auditorIds,
    ): int {
        $this->setRoute('tasks.create');

        $responsibleId = self::$RESPONSIBLE_BOT;
        if (empty($auditorIds)) {
            $auditorIds = self::$DEFAULT_AUDITORS;
        }

        $fields = [
            'TITLE' => $request->post('name'),
            'DESCRIPTION' => $request->post('description'),
            'PRIORITY' => $request->post('priority_id'),
            'RESPONSIBLE_ID' => $responsibleId,
            'ACCOMPLICES' => $responsibleIds,
            'AUDITORS' => $auditorIds,
            'STATUS' => TaskStatusesEnum::NEW->value,
            'DATE_START' => Carbon::now()->toDateTimeString(),
            'GROUP_ID' => Auth::user()->b_group_id,
            'CREATED_BY' => self::CREATED_BY_USER,
            'CREATED_DATE' => Carbon::now()->toDateTimeString(),
        ];
        $this->addFields($fields);
        $response = $this->execute();

        return $response['result']['task']['id'];
    }

    public function uploadFiles(
        int $bTaskId, array $filesIds
    ): void {
        $this->setRoute('tasks.files.attach');
        foreach ($filesIds as $fileId) {
            $this->setBodyData([
                'taskId' => $bTaskId,
                'fileId' => $fileId,
            ]);
            $this->execute();
        }
    }

    public function addFirstComment(
        int $bTaskId,
        Request $request,
        string $problemTypeName,
        string|null $problemName,
        int $contactId = 0
    ): void {
        $this->setRoute('tasks.comments.add');
        $fields = [
            'TASKID' => $bTaskId,
            'FIELDS' => [
                'POST_MESSAGE' => $this->getCommentFromRequest($request, $problemTypeName, $problemName, $contactId),
                'AUTHOR_ID' => self::CREATED_BY_USER,
                'POST_DATE' => Carbon::now()->toDateTimeString(),
            ],
        ];
        $this->setBodyData($fields);
        $this->execute();
    }

    private function getCommentFromRequest(
        Request $request,
        string $problemTypeName,
        string|null $problemName,
        int $contactId = 0
    ): string {
        $commentLines = [
            "Контактное лицо: {$request->post('person')} - {$request->post('person_phone')}",
            "Контакт: https://bitrix.567150.ru/crm/contact/details/{$contactId}/",
            "Тип проблемы: {$problemTypeName}",
        ];
        if (!empty($problemName)) {
            $commentLines[] = "Проблема: {$problemName}";
        }
        $commentParams = [
            'document_number' => 'Номер договора',
            'document_cost' => 'Сумма договора',
            'document_contractor' => 'Контрагент договора',
            'equipment_model' => 'Модель устройства',
            'equipment_number' => 'Номер устройства',
            'one_c_config' => 'Конфигурация 1С',
            'one_c_version' => 'Версия 1С',
            'one_c_link' => 'Ссылка на обработку',
            'site_link' => 'Ссылка на сайт',
            'site_os' => 'Операционная система',
            'site_browser' => 'Браузер',
        ];
        foreach ($commentParams as $key => $param) {
            if ($request->filled($key)) {
                $commentLines[] = "{$param}: {$request->post($key)}";
            }
        }

        return '<p> ' . implode(" </p><p> ", $commentLines) . ' </p>';
    }
}
