<?php

namespace App\Models\Tasks;

use App\Models\PrefixedModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskProblemTypeProblem extends PrefixedModel
{
    public $table = 'task_problem_type_problems';

    protected $fillable = [
        'id',
        'name',
        'problem_type_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @return BelongsTo
     */
    public function problemType(): BelongsTo
    {
        return $this->belongsTo(
            TaskProblemType::class,
            'problem_type_id',
            'id'
        );
    }
}
