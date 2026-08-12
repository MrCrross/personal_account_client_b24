<?php

namespace App\Models\Bitrix24\Disk;

use Illuminate\Database\Eloquent\Model;

/**
 * Read-only зеркало b_disk_attached_object — привязки файлов Битрикс-Диска к сущностям
 * (задача, результат, комментарий) через ENTITY_TYPE + ENTITY_ID.
 *
 * ID строки = число из маркера [DISK FILE ID=nXXX] в attached-object-варианте;
 * OBJECT_ID = сам файл Диска (по нему дедуплицируем — один файл может быть привязан несколько раз).
 */
class BDiskAttachedObject extends Model
{
    protected $table = 'b_disk_attached_object';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'OBJECT_ID',
        'ENTITY_TYPE',
        'ENTITY_ID',
    ];
}
