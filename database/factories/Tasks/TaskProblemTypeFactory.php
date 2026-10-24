<?php

namespace Database\Factories\Tasks;

use App\Models\Tasks\TaskProblemType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskProblemType>
 */
class TaskProblemTypeFactory extends Factory
{
    protected $model = TaskProblemType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
        ];
    }
}
