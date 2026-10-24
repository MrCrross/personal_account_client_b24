<?php

namespace App\Http\Wrappers;

use App\Http\Modules\Bitrix\Repositories\BDiskAttachedObjectsRepository;
use App\Models\Bitrix24\Crm\BCrmContact;
use App\Models\Bitrix24\Disk\BDiskAttachedObject;
use App\Models\Bitrix24\Disk\BDiskObject;
use App\Models\Bitrix24\Forum\BForumMessage;
use App\Models\Bitrix24\Task\BTask;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Локальный драйвер Bitrix24 (B24_DRIVER=database): вместо REST-вызовов через вебхук пишет и
 * читает локальные таблицы b_* (миграция create_local_bitrix_mirror_tables). Для разработки
 * без доступа к порталу.
 *
 * Эмулирует только методы, которые реально вызывают сервисы модуля Bitrix, и возвращает ответ
 * в той же форме, что и REST Bitrix24, — поэтому сервисы о драйвере не знают.
 *
 * Содержимое «загруженных на Диск» файлов лежит на диске local в DISK_DIR/<ID b_disk_object>/<имя>.
 */
class BLocalRest
{
    private const DISK_DIR = 'b24-local/disk';

    private const DOWNLOAD_URL_PREFIX = 'b24-local://disk/';

    public function __construct()
    {
        // На бою b_* — настоящие таблицы портала, писать в них напрямую нельзя
        if (app()->isProduction()) {
            throw new RuntimeException('B24_DRIVER=database недоступен в production');
        }
    }

    /**
     * @param  string  $method  имя REST-метода Bitrix24 (значение из config/b24.php routes)
     */
    public function call(string $method, array $body): array
    {
        return match ($method) {
            'tasks.task.add' => $this->addTask($body['fields'] ?? []),
            'tasks.task.update' => $this->updateTask((int) $body['taskId'], $body['fields'] ?? []),
            'tasks.task.files.attach' => $this->attachFileToTask((int) $body['taskId'], (int) $body['fileId']),
            'task.commentitem.add' => $this->addComment((int) $body['TASKID'], $body['FIELDS'] ?? []),
            'crm.contact.add' => $this->addContact($body['fields'] ?? []),
            'disk.storage.uploadfile' => $this->uploadFile($body),
            'disk.attachedObject.get' => $this->getAttachedObject((int) $body['id']),
            'disk.file.get' => $this->getFile((int) $body['id']),
            default => throw new RuntimeException("Метод {$method} не поддерживается в B24_DRIVER=database"),
        };
    }

    /**
     * Аналог скачивания по DOWNLOAD_URL: отдаёт содержимое локального файла Диска.
     */
    public function download(string $url): Response
    {
        if (! str_starts_with($url, self::DOWNLOAD_URL_PREFIX)) {
            throw new RuntimeException("Неизвестный DOWNLOAD_URL для B24_DRIVER=database: {$url}");
        }

        $object = BDiskObject::query()
            ->select('ID', 'NAME')
            ->where('ID', '=', (int) substr($url, strlen(self::DOWNLOAD_URL_PREFIX)))
            ->first();
        $path = $object !== null ? $this->diskPath($object->ID, $object->NAME) : null;
        $disk = Storage::disk('local');
        if ($path === null || ! $disk->exists($path)) {
            return new Response(new PsrResponse(404));
        }

        return new Response(new PsrResponse(
            200,
            ['Content-Type' => $disk->mimeType($path) ?: 'application/octet-stream'],
            $disk->get($path),
        ));
    }

    private function addTask(array $fields): array
    {
        $now = Carbon::now()->toDateTimeString();

        // ACCOMPLICES/AUDITORS в Битриксе живут в b_tasks_member — проект их не читает, не храним
        $id = BTask::query()->insertGetId([
            'TITLE' => $fields['TITLE'] ?? null,
            'DESCRIPTION' => $fields['DESCRIPTION'] ?? null,
            'PRIORITY' => $fields['PRIORITY'] ?? 1,
            'STATUS' => $fields['STATUS'] ?? null,
            'RESPONSIBLE_ID' => $fields['RESPONSIBLE_ID'] ?? null,
            'GROUP_ID' => $fields['GROUP_ID'] ?? null,
            'CREATED_BY' => $fields['CREATED_BY'] ?? null,
            'CREATED_DATE' => $now,
            'CHANGED_BY' => $fields['CREATED_BY'] ?? null,
            'CHANGED_DATE' => $now,
            'ACTIVITY_DATE' => $now,
        ]);

        return ['result' => ['task' => ['id' => $id]]];
    }

    /**
     * Меняет только поля, которые реально шлёт BTasksService::update; изменения от имени бота.
     */
    private function updateTask(int $taskId, array $fields): array
    {
        $task = BTask::query()->select('ID')->where('ID', '=', $taskId)->first();
        if ($task === null) {
            return $this->notFound();
        }

        $now = Carbon::now()->toDateTimeString();
        // toBase(): у b_tasks нет updated_at, а Eloquent-update дописал бы его сам
        BTask::query()->where('ID', '=', $taskId)->toBase()->update([
            ...array_intersect_key($fields, array_flip(['TITLE', 'DESCRIPTION', 'PRIORITY'])),
            'CHANGED_BY' => config('b24.createdByUser'),
            'CHANGED_DATE' => $now,
            'ACTIVITY_DATE' => $now,
        ]);

        return ['result' => ['task' => ['id' => $taskId]]];
    }

    private function attachFileToTask(int $taskId, int $fileId): array
    {
        $id = BDiskAttachedObject::query()->insertGetId([
            'OBJECT_ID' => $fileId,
            'MODULE_ID' => 'tasks',
            'ENTITY_TYPE' => ltrim(BDiskAttachedObjectsRepository::ENTITY_TYPE_TASK, '\\'),
            'ENTITY_ID' => $taskId,
            'CREATE_TIME' => Carbon::now()->toDateTimeString(),
            'CREATED_BY' => config('b24.createdByUser'),
        ]);

        return ['result' => ['attachmentId' => $id]];
    }

    /**
     * Вложения комментария (UF_FORUM_MESSAGE_DOC) не привязываем: переписку из Битрикса не читаем,
     * а ID файлов сообщения проект хранит сам (task_messages.b_disk_file_ids).
     */
    private function addComment(int $taskId, array $fields): array
    {
        $id = BForumMessage::query()->insertGetId([
            'XML_ID' => 'TASK_'.$taskId,
            'AUTHOR_ID' => $fields['AUTHOR_ID'] ?? null,
            'POST_MESSAGE' => $fields['POST_MESSAGE'] ?? '',
            'POST_DATE' => Carbon::now()->toDateTimeString(),
        ]);

        return ['result' => $id];
    }

    private function addContact(array $fields): array
    {
        $id = BCrmContact::query()->insertGetId([
            'NAME' => $fields['NAME'] ?? null,
            'LAST_NAME' => $fields['LAST_NAME'] ?? null,
            'SECOND_NAME' => $fields['SECOND_NAME'] ?? null,
            'COMPANY_ID' => $fields['COMPANY_ID'] ?? null,
            'OPENED' => $fields['OPENED'] ?? 'N',
            'ASSIGNED_BY_ID' => $fields['ASSIGNED_BY_ID'] ?? null,
            'DATE_CREATE' => Carbon::now()->toDateTimeString(),
        ]);

        // Порядок «Фамилия Имя Отчество телефон» — как ищет BCrmContactRepository::searchContact
        $searchContent = implode(' ', array_filter([
            $id,
            $fields['LAST_NAME'] ?? null,
            $fields['NAME'] ?? null,
            $fields['SECOND_NAME'] ?? null,
            $fields['PHONE'] ?? null,
        ]));
        BCrmContact::query()->where('ID', '=', $id)->update(['SEARCH_CONTENT' => $searchContent]);

        return ['result' => $id];
    }

    private function uploadFile(array $body): array
    {
        $content = base64_decode($body['fileContent'] ?? '', true);
        if ($content === false) {
            throw new RuntimeException('disk.storage.uploadfile: fileContent не в base64');
        }
        $name = $body['data']['NAME'] ?? 'file';

        $id = BDiskObject::query()->insertGetId([
            'STORAGE_ID' => $body['id'] ?? null,
            'NAME' => $name,
            'SIZE' => strlen($content),
            'CREATE_TIME' => Carbon::now()->toDateTimeString(),
            'CREATED_BY' => config('b24.createdByUser'),
        ]);
        Storage::disk('local')->put($this->diskPath($id, $name), $content);

        return ['result' => ['ID' => $id, 'NAME' => $name]];
    }

    private function getAttachedObject(int $id): array
    {
        $objectId = BDiskAttachedObject::query()->where('ID', '=', $id)->value('OBJECT_ID');

        return $objectId !== null ? $this->getFile((int) $objectId) : $this->notFound();
    }

    private function getFile(int $id): array
    {
        $object = BDiskObject::query()->select('ID', 'NAME', 'SIZE')->where('ID', '=', $id)->first();
        if ($object === null) {
            return $this->notFound();
        }

        return ['result' => [
            'ID' => $object->ID,
            'NAME' => $object->NAME,
            'SIZE' => $object->SIZE,
            'DOWNLOAD_URL' => self::DOWNLOAD_URL_PREFIX.$object->ID,
        ]];
    }

    private function notFound(): array
    {
        return ['error' => 'ERROR_NOT_FOUND', 'error_description' => 'Could not find entity with id'];
    }

    /**
     * Имя файла в пути сохраняем — по расширению определяется Content-Type при скачивании.
     */
    private function diskPath(int $diskObjectId, string $name): string
    {
        return self::DISK_DIR.'/'.$diskObjectId.'/'.basename($name);
    }
}
