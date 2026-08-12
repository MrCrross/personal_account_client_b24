<?php

namespace App\Http\Modules\Bitrix\Services;

use App\Http\Wrappers\BWebHookConstructor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BDiskService extends BWebHookConstructor
{
    private const STORAGE_ID = 344; // БОТ ФЛЕШ-ФОРМАТ

    /**
     * @param  array  $files  - массив UploadFile
     * @param  int  $taskId  - для подписи названия файла
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function store(array $files, int $taskId): array
    {
        $filesIds = [];
        $this->setRoute('disk.upload');
        foreach ($files as $key => $file) {
            $fields = [
                'id' => self::STORAGE_ID,
                'data' => [
                    'NAME' => 'task_'.$taskId.'_file_'.($key + 1).'.'.$file->extension(),
                ],
                'fileContent' => base64_encode($file->get()),
                'generateUniqueName' => true,
            ];
            $this->setBodyData($fields);
            $response = $this->execute();
            $filesIds[] = $response['result']['ID'];
        }

        return $filesIds;
    }

    /**
     * Данные прикреплённого файла Диска по ID (числовая часть маркера [DISK FILE ID=nXXX] в тексте задачи).
     *
     * @return array{name: string, size: int, download_url: string}|null
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function getAttachedObject(int $id): ?array
    {
        // Маркер [DISK FILE ID=nXXX] в Битриксе бывает и attached-object id, и disk file id —
        // пробуем оба метода, чтобы не зависеть от того, как именно прикрепили файл.
        return $this->fetchDiskObject('disk.attachedObject', $id)
            ?? $this->fetchDiskObject('disk.file', $id);
    }

    /**
     * @return array{name: string, size: int, download_url: string}|null
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    private function fetchDiskObject(string $route, int $id): ?array
    {
        $this->setRoute($route);
        $this->setBodyData(['id' => $id]);
        $response = $this->execute();

        $object = is_array($response) ? ($response['result'] ?? null) : null;
        if (empty($object) || empty($object['DOWNLOAD_URL'])) {
            // Не смогли разрешить файл — логируем сырой ответ Битрикса, иначе провал молчаливый
            Log::warning('Bitrix disk resolve failed', [
                'method' => config('b24.routes.'.$route),
                'id' => $id,
                'response' => is_array($response) ? $response : (string) $response->body(),
            ]);

            return null;
        }

        return [
            'name' => $object['NAME'] ?? 'file',
            'size' => (int) ($object['SIZE'] ?? 0),
            'download_url' => $object['DOWNLOAD_URL'],
        ];
    }

    /**
     * Скачать содержимое прикреплённого файла Диска. DOWNLOAD_URL уже содержит auth-токен,
     * поэтому тянем его напрямую и стримим клиенту, не раскрывая токен в браузер.
     *
     * @return array{name: string, content_type: string, body: string}|null
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function downloadAttachedObject(int $attachedObjectId): ?array
    {
        $object = $this->getAttachedObject($attachedObjectId);
        if ($object === null) {
            return null;
        }

        $response = Http::get($object['download_url']);
        if (! $response->successful()) {
            return null;
        }

        return [
            'name' => $object['name'],
            'content_type' => $response->header('Content-Type') ?: 'application/octet-stream',
            'body' => $response->body(),
        ];
    }
}
