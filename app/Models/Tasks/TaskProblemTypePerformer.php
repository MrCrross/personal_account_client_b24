<?php

namespace App\Models\Tasks;

use App\Models\Bitrix24\BUser;
use App\Models\PrefixedModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskProblemTypePerformer extends PrefixedModel
{
    public $table = 'task_problem_type_performers';

    protected $fillable = [
        'id',
        'problem_type_id',
        'b_user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            BUser::class,
            'b_user_id',
            'ID'
        )->select(['ID', 'NAME', 'LAST_NAME', 'LOGIN']);
    }
}
