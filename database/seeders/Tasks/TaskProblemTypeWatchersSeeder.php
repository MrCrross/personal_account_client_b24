<?php

namespace Database\Seeders\Tasks;

use App\Models\Tasks\TaskProblemTypeWatcher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TaskProblemTypeWatchersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $problemTypesWatchers = [
            [
                "problem_type_id" => 1,
                "b_user_id" => 1, // Никита Залесский
            ],
            [
                "problem_type_id" => 2,
                "b_user_id" => 1, // Никита Залесский
            ],
            [
                "problem_type_id" => 3,
                "b_user_id" => 1, // Никита Залесский
            ],
            [
                "problem_type_id" => 4,
                "b_user_id" => 1, // Никита Залесский
            ],
            [
                "problem_type_id" => 5,
                "b_user_id" => 1, // Никита Залесский
            ],
            [
                "problem_type_id" => 6,
                "b_user_id" => 1, // Никита Залесский
            ],
        ];
        foreach ($problemTypesWatchers as $key => $value) {
            $problemTypesWatchers[$key]['created_at'] = Carbon::now();
        }
        TaskProblemTypeWatcher::insert($problemTypesWatchers);
    }
}
