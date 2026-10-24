<?php

namespace App\Models\Bitrix24\Disk;

use Illuminate\Database\Eloquent\Model;

/**
 * Зеркало b_disk_object — файлы Битрикс-Диска. Код проекта его не читает:
 * пишет только локальный драйвер B24_DRIVER=database (App\Http\Wrappers\BLocalRest).
 */
class BDiskObject extends Model
{
    protected $table = 'b_disk_object';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'STORAGE_ID',
        'NAME',
        'TYPE',
        'SIZE',
        'CREATE_TIME',
        'CREATED_BY',
    ];
}
