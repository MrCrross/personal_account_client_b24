<?php

namespace App\Models\Tasks;

use App\Models\PrefixedModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskProblemType extends PrefixedModel
{
    use HasFactory;

    public $table = 'task_problem_types';

    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];

    public function problems(): HasMany
    {
        return $this->hasMany(
            TaskProblemTypeProblem::class,
            'problem_type_id',
            'id'
        )->select('id', 'problem_type_id', 'name');
    }

    public function performers(): HasMany
    {
        return $this->hasMany(
            TaskProblemTypePerformer::class,
            'problem_type_id',
            'id'
        )->select('id', 'problem_type_id', 'b_user_id');
    }

    public function watchers(): HasMany
    {
        return $this->hasMany(
            TaskProblemTypeWatcher::class,
            'problem_type_id',
            'id'
        )->select('id', 'problem_type_id', 'b_user_id');
    }
}
