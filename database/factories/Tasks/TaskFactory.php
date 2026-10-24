<?php

namespace Database\Factories\Tasks;

use App\Models\Tasks\Task;
use App\Models\Tasks\TaskProblemType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'person' => fake()->name(),
            'person_phone' => fake()->numerify('89#########'),
            'b_task_id' => fake()->numberBetween(1000, 999999),
            'user_creator_id' => User::factory(),
            'problem_type_id' => TaskProblemType::factory(),
        ];
    }
}
