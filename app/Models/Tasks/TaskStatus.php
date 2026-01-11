<?php

namespace App\Models\Tasks;

use App\Models\PrefixedModel;

class TaskStatus extends PrefixedModel
{
    public $table = 'task_statuses';

    protected $fillable = [
        'b_status',
        'name',
    ];
}
