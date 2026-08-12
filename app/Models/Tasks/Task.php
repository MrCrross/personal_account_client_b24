<?php

namespace App\Models\Tasks;

use App\Models\Bitrix24\Task\BTask;
use App\Models\Bitrix24\Task\BTasksResult;
use App\Models\PrefixedModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends PrefixedModel
{
    public $table = 'tasks';

    use HasFactory;

    protected $fillable = [
        'id',
        'person',
        'person_phone',
        'b_task_id',
        'user_creator_id',
        'created_at',
        'updated_at',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_creator_id',
            'id',
        );
    }

    public function bTask(): BelongsTo
    {
        return $this->belongsTo(
            BTask::class,
            'b_task_id',
            'ID'
        );
    }

    public function bResult(): HasMany
    {
        return $this->hasMany(
            BTasksResult::class,
            'TASK_ID',
            'b_task_id',
        );
    }

    public function problemType(): BelongsTo
    {
        return $this->belongsTo(
            TaskProblemType::class,
            'problem_type_id',
            'id'
        );
    }

    /**
     * Локальная переписка по обращению (в Битрикс уходит копией-комментарием).
     */
    public function messages(): HasMany
    {
        return $this->hasMany(
            TaskMessage::class,
            'task_id',
            'id',
        );
    }
}
