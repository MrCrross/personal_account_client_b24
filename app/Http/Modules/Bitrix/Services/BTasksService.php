<?php

namespace App\Http\Modules\Bitrix\Services;

use App\Http\Modules\Tasks\Enums\TaskStatusesEnum;
use App\Http\Wrappers\BWebHookConstructor;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class BTasksService extends BWebHookConstructor
{
    public function store(
        Request $request,
        array $responsibleIds,
        array $auditorIds,
    ): int {
        $this->setRoute('tasks.create');

        $responsibleId = config('b24.default.responsibleId');
        if (empty($auditorIds)) {
            $auditorIds = config('b24.default.auditors');
        }

        $fields = [
            'TITLE' => $request->post('name'),
            'DESCRIPTION' => $request->post('description'),
            'PRIORITY' => $request->post('priority_id'),
            'RESPONSIBLE_ID' => $responsibleId,
            'ACCOMPLICES' => $responsibleIds,
            'AUDITORS' => $auditorIds,
            'STATUS' => TaskStatusesEnum::NEW->value,
            //            'DATE_START' => Carbon::now()->toDateTimeString(),
            'GROUP_ID' => Auth::user()->b_group_id,
            'CREATED_BY' => $this->createdByUser,
            //            'CREATED_DATE' => Carbon::now()->toDateTimeString(),
        ];
        $this->addFields($fields);
        $response = $this->execute();

        return $response['result']['task']['id'];
    }

    /**
     * Обновить поля задачи в Битриксе (TITLE, DESCRIPTION, PRIORITY и т.п.).
     * Данные задачи живут в Битриксе, поэтому ошибку не глушим — бросаем исключение.
     *
     * @throws ConnectionException
     * @throws RuntimeException
     */
    public function update(int $bTaskId, array $fields): void
    {
        $this->setRoute('tasks.update');
        $this->setBodyData([
            'taskId' => $bTaskId,
            'fields' => $fields,
        ]);
        $response = $this->execute();

        if (! is_array($response) || isset($response['error']) || ! isset($response['result'])) {
            throw new RuntimeException("Битрикс24 не обновил задачу {$bTaskId}");
        }
    }

    public function uploadFiles(
        int $bTaskId,
        array $filesIds
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
        ?string $problemName,
        int $contactId = 0
    ): void {
        $this->setRoute('tasks.comments.add');
        $fields = [
            'TASKID' => $bTaskId,
            'FIELDS' => [
                'POST_MESSAGE' => $this->getCommentFromRequest($request, $problemTypeName, $problemName, $contactId),
                'AUTHOR_ID' => $this->createdByUser,
                //                'POST_DATE' => Carbon::now()->toDateTimeString(),
            ],
        ];
        $this->setBodyData($fields);
        $this->execute();
    }

    private function getCommentFromRequest(
        Request $request,
        string $problemTypeName,
        ?string $problemName,
        int $contactId = 0
    ): string {
        $b24Domain = rtrim(config('b24.domain'), '/');
        $commentLines = [
            "Контактное лицо: {$request->post('person')} - {$request->post('person_phone')}",
            'Контакт: ' . $b24Domain . "/crm/contact/details/{$contactId}/",
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

        return '<p> ' . implode(' </p><p> ', $commentLines) . ' </p>';
    }

    /**
     * Добавить комментарий в задачу Битрикса от имени бота.
     * Вложения передаются ID файлов Битрикс-Диска (поле UF_FORUM_MESSAGE_DOC ждёт префикс «n»).
     * Возвращает ID созданного комментария или null, если Битрикс его не вернул.
     *
     * @param int[] $diskFileIds
     *
     * @throws ConnectionException
     */
    public function addComment(int $bTaskId, string $message, array $diskFileIds = []): ?int
    {
        $this->setRoute('tasks.comments.add');
        $fields = [
            'POST_MESSAGE' => $message,
            'AUTHOR_ID' => $this->createdByUser,
        ];
        if (!empty($diskFileIds)) {
            $fields['UF_FORUM_MESSAGE_DOC'] = array_map(static fn($id) => 'n' . $id, $diskFileIds);
        }
        $this->setBodyData([
            'TASKID' => $bTaskId,
            'FIELDS' => $fields,
        ]);
        $response = $this->execute();

        return $response['result'] ?? null;
    }
}
