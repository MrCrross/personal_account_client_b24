<?php

namespace App\Models\Bitrix24\Forum;

use Illuminate\Database\Eloquent\Model;

/**
 * Зеркало b_forum_message — комментарии задач Битрикса. Переписку из Битрикса не читаем:
 * пишет сюда только локальный драйвер B24_DRIVER=database (App\Http\Wrappers\BLocalRest).
 */
class BForumMessage extends Model
{
    protected $table = 'b_forum_message';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'XML_ID',
        'AUTHOR_ID',
        'POST_MESSAGE',
        'POST_DATE',
    ];
}
