<?php

namespace App\Models\Bitrix24\Task;

use App\Models\Bitrix24\BSonetGroup;
use App\Models\Bitrix24\BUser;
use App\Models\Tasks\TaskPriority;
use App\Models\Tasks\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BTask extends Model
{
    protected $table = 'b_tasks';
    protected $fillable = [
        'ID',
        'TITLE',
        'DESCRIPTION',
        'PRIORITY',
        'STATUS',
        'GROUP_ID',
        'RESPONSIBLE_ID',
        'CHANGED_DATE',
        'CLOSED_DATE',
        'DATE_START',
        'DEADLINE',
        'START_DATE_PLAN',
        'END_DATE_PLAN',
        'DURATION_PLAN',
        'DURATION_FACT',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            BUser::class,
            'RESPONSIBLE_ID',
            'ID'
        );
    }


    /**
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(
            BSonetGroup::class,
            'GROUP_ID',
            'ID'
        );
    }

    /**
     * @return HasMany
     */
    public function result(): HasMany
    {
        return $this->hasMany(
            BTasksResult::class,
            'TASK_ID',
            'ID',
        );
    }

    /**
     * @return BelongsTo
     */
    public function taskStatus(): BelongsTo
    {
        return $this->belongsTo(
            TaskStatus::class,
            'STATUS',
            'b_status'
        );
    }


    /**
     * @return BelongsTo
     */
    public function taskPriority(): BelongsTo
    {
        return $this->belongsTo(
            TaskPriority::class,
            'PRIORITY',
            'b_priority'
        );
    }
}
