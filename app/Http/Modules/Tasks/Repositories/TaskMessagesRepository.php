<?php

namespace App\Http\Modules\Tasks\Repositories;

use App\Models\Tasks\TaskMessage;

class TaskMessagesRepository
{
    public static function store(array $fields): int
    {
        return TaskMessage::query()->insertGetId($fields);
    }

    public static function getById(int $id): ?TaskMessage
    {
        return TaskMessage::query()
            ->with('user')
            ->where('id', '=', $id)
            ->first();
    }
}
