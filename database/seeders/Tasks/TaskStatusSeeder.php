<?php

namespace Database\Seeders\Tasks;

use App\Models\Tasks\TaskPriority;
use App\Models\Tasks\TaskStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TaskStatusSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $statuses = [
            [
                'b_status' => -1,
                'name' => 'Просроченная задача',
                'variant' => 'destructive',
            ],
            [
                'b_status' => -2,
                'name' => 'Непросмотренная задача',
                'variant' => 'default',
            ],
            [
                'b_status' => -3,
                'name' => 'Почти просрочена',
                'variant' => 'warning',
            ],
            [
                'b_status' => 1,
                'name' => 'Новая задача',
                'variant' => 'default',
            ],
            [
                'b_status' => 2,
                'name' => 'Задача принята ответственным',
                'variant' => 'default',
            ],
            [
                'b_status' => 3,
                'name' => 'Задача выполняется',
                'variant' => 'success',
            ],
            [
                'b_status' => 4,
                'name' => 'Условно завершена (Ждёт согласования)',
                'variant' => 'success',
            ],
            [
                'b_status' => 5,
                'name' => 'Задача выполнена',
                'variant' => 'success',
            ],
            [
                'b_status' => 6,
                'name' => 'Задача отложена',
                'variant' => 'warning',
            ],
            [
                'b_status' => 7,
                'name' => 'Задача отклонена ответственным',
                'variant' => 'warning',
            ],
        ];
        TaskStatus::insert($statuses);

        $priorities = [
            [
                'b_priority' => 0,
                'name' => 'Низкий',
            ],
            [
                'b_priority' => 1,
                'name' => 'Средний',
            ],
            [
                'b_priority' => 2,
                'name' => 'Высокий',
            ],
        ];
        TaskPriority::insert($priorities);
    }
}
