<?php

namespace App\Http\Modules\Bitrix\Repositories;

use App\Models\Bitrix24\Disk\BDiskAttachedObject;
use Illuminate\Support\Facades\Schema;

/**
 * Read-only доступ к привязкам файлов Битрикс-Диска (b_disk_attached_object).
 *
 * Таблица — внешняя (Bitrix), в проектных миграциях не создаётся: в тестах с :memory:
 * и на локали без зеркала её нет — методы отдают [].
 */
class BDiskAttachedObjectsRepository
{
    /**
     * Connector задачи: файлы, прикреплённые к описанию задачи.
     * ENTITY_ID = b_tasks.ID.
     *
     * Значение — имя PHP-класса коннектора, именно так реально лежит в колонке
     * ENTITY_TYPE (без ведущего «\» — см. ltrim в objectIdsForEntity). Проверено прямым
     * запросом к b_disk_attached_object на бою: disk.attachedObject.get отдаёт другой,
     * короткий код ("tasks_task") — это нормализация REST-слоя, в таблице её нет.
     */
    public const ENTITY_TYPE_TASK = '\\Bitrix\\Tasks\\Integration\\Disk\\Connector\\Task';

    /**
     * Connector результата задачи: файлы, прикреплённые к тексту результата.
     * ENTITY_ID = b_tasks_result.ID.
     *
     * Значение — имя PHP-класса коннектора (см. ENTITY_TYPE_TASK).
     */
    public const ENTITY_TYPE_TASK_RESULT = '\\Bitrix\\Tasks\\Integration\\Disk\\Connector\\Task\\Result';

    /**
     * Возвращает ID attached-object'ов (колонка b_disk_attached_object.ID) для сущности
     * указанного типа, дедуплицированные по OBJECT_ID — один файл может быть привязан
     * к одной сущности несколько раз, качать/резолвить его нужно один раз.
     *
     * Порядок — по возрастанию ID (порядок прикрепления).
     *
     * @param  string  $type  connector class (см. константы ENTITY_TYPE_*)
     * @param  int|int[]  $entityIds
     * @return int[]
     */
    public static function objectIdsForEntity(string $type, int|array $entityIds): array
    {
        if (! Schema::hasTable((new BDiskAttachedObject)->getTable())) {
            return [];
        }

        $entityIds = array_map('intval', (array) $entityIds);
        if (empty($entityIds)) {
            return [];
        }

        // В b_disk_attached_object ENTITY_TYPE может лежать класс как с ведущим «\», так и без —
        // принимаем обе формы.
        $types = array_values(array_unique([$type, ltrim($type, '\\')]));

        $rows = BDiskAttachedObject::query()
            ->select('ID', 'OBJECT_ID')
            ->whereIn('ENTITY_ID', $entityIds)
            ->whereIn('ENTITY_TYPE', $types)
            ->orderBy('ID')
            ->get();

        $ids = [];
        $seenObjects = [];
        foreach ($rows as $row) {
            $objectId = $row->OBJECT_ID;
            if (isset($seenObjects[$objectId])) {
                continue;
            }
            $seenObjects[$objectId] = true;
            $ids[] = (int) $row->ID;
        }

        return $ids;
    }
}
