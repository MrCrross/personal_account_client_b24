<?php

namespace App\Models\Tasks;

use App\Models\PrefixedModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskMessage extends PrefixedModel
{
    public $table = 'task_messages';

    protected $fillable = [
        'task_id',
        'user_id',
        'text',
        'b_comment_id',
        'b_disk_file_ids',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        // ID вложений Битрикс-Диска; сам контент не храним — резолвим из Битрикса по запросу
        'b_disk_file_ids' => 'array',
    ];

    /**
     * Автор сообщения (клиент личного кабинета).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id',
        );
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(
            Task::class,
            'task_id',
            'id',
        );
    }
}
