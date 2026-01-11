<?php

namespace App\Models\Bitrix24\Task;

use App\Models\Bitrix24\BUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BTasksResult extends Model
{
    protected $table = 'b_tasks_result';

    protected $fillable = [
        'ID',
        'TASK_ID',
        'TEXT',
        'CREATED_BY',
        'CREATED_AT',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            BUser::class,
            'CREATED_BY',
            'ID'
        );
    }
}
