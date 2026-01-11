<?php

namespace App\Http\Modules\Bitrix\Services;

use App\Http\Wrappers\BWebHookConstructor;

class BDiskService extends BWebHookConstructor
{
    private const STORAGE_ID = 344; // БОТ ФЛЕШ-ФОРМАТ

    /**
     * @param array $files - массив UploadFile
     * @param int $taskId - для подписи названия файла
     * @return array
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
                    'NAME' => 'task_' . $taskId . '_file_' . ($key + 1) . '.' . $file->extension(),
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
}
