<?php

namespace App\Models\Tasks;

use App\Models\PrefixedModel;

class TaskPriority extends PrefixedModel
{
    public $table = 'task_priorities';

    protected $fillable = [
        'id',
        'name'
    ];
}
