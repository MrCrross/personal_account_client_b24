<?php

namespace Database\Seeders\Tasks;

use App\Models\Tasks\TaskProblemType;
use App\Models\Tasks\TaskProblemTypePerformer;
use App\Models\Tasks\TaskProblemTypeProblem;
use App\Models\Tasks\TaskProblemTypeWatcher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TaskProblemTypesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $taskProblemTypeWatcherTable = (new TaskProblemTypeWatcher())->getTable();
        $taskProblemTypePerformerTable = (new TaskProblemTypePerformer())->getTable();
        $taskProblemTypeProblemTable = (new TaskProblemTypeProblem())->getTable();
        $taskProblemTypeTable = (new TaskProblemType())->getTable();
        TaskProblemTypeWatcher::query()->truncate();
        TaskProblemTypePerformer::query()->truncate();
        TaskProblemTypeProblem::query()->truncate();
        TaskProblemType::query()->truncate();
        DB::statement("ALTER TABLE {$taskProblemTypeWatcherTable} AUTO_INCREMENT = 1;");
        DB::statement("ALTER TABLE {$taskProblemTypePerformerTable} AUTO_INCREMENT = 1;");
        DB::statement("ALTER TABLE {$taskProblemTypeProblemTable} AUTO_INCREMENT = 1;");
        DB::statement("ALTER TABLE {$taskProblemTypeTable} AUTO_INCREMENT = 1;");

        $problemTypes = [
            [
                'name' => 'Проблема в 1С БУХ/ЗУП/УТ',
            ],
            [
                'name' => 'Проблема с техникой, сетью',
            ],
            [
                'name' => 'Проблема с 1С сервером',
            ],
            [
                'name' => 'Проблема с сайтом, интернет-магазином',
            ],
            [
                'name' => 'Обращение по битрикс, битрикс24',
            ],
            [
                'name' => 'Прочее',
            ],
        ];
        foreach ($problemTypes as $key => $value) {
            $problemTypes[$key]['created_at'] = Carbon::now();
        }
        TaskProblemType::insert($problemTypes);

        $problemTypesProblems = [
//            [
//                'problem_type_id' => 1,
//                'name' => 'Акт',
//            ],
//            [
//                'problem_type_id' => 1,
//                'name' => 'Счет',
//            ],
//            [
//                'problem_type_id' => 1,
//                'name' => 'Договор',
//            ],
//            [
//                'problem_type_id' => 1,
//                'name' => 'Закрывающие документы',
//            ],
//            [
//                'problem_type_id' => 1,
//                'name' => 'Другое (указано в описании)',
//            ],
            [
                'problem_type_id' => 2,
                'name' => 'Программное обеспечение',
            ],
            [
                'problem_type_id' => 2,
                'name' => 'ПК и периферия',
            ],
            [
                'problem_type_id' => 2,
                'name' => 'Локальная сеть/Ethernet',
            ],
            [
                'problem_type_id' => 2,
                'name' => 'Принтеры',
            ],
            [
                'problem_type_id' => 2,
                'name' => 'Проблема с доступом',
            ],
            [
                'problem_type_id' => 2,
                'name' => 'Другое (указано в описании)',
            ],
            [
                'problem_type_id' => 3,
                'name' => 'Доработка',
            ],
            [
                'problem_type_id' => 3,
                'name' => 'Отчет',
            ],
            [
                'problem_type_id' => 3,
                'name' => 'Обновление',
            ],
            [
                'problem_type_id' => 3,
                'name' => 'Исправление',
            ],
            [
                'problem_type_id' => 3,
                'name' => 'Другое (указано в описании)',
            ],
        ];
        foreach ($problemTypesProblems as $key => $value) {
            $problemTypesProblems[$key]['created_at'] = Carbon::now();
        }
        TaskProblemTypeProblem::insert($problemTypesProblems);

        $problemTypesPerformers = [
            [
                "problem_type_id" => 1,
                "b_user_id" => 200, // Людмила
            ],
            [
                "problem_type_id" => 1,
                "b_user_id" => 633, // Александр
            ],
            [
                "problem_type_id" => 2,
                "b_user_id" => 198, // Артемий
            ],
            [
                "problem_type_id" => 2,
                "b_user_id" => 17, // Павел
            ],
            [
                "problem_type_id" => 3,
                "b_user_id" => 17, // Павел
            ],
            [
                "problem_type_id" => 3,
                "b_user_id" => 633, // Александр
            ],
            [
                "problem_type_id" => 3,
                "b_user_id" => 198, // Артемий
            ],
            [
                "problem_type_id" => 4,
                "b_user_id" => 742, // Полина
            ],
            [
                "problem_type_id" => 4,
                "b_user_id" => 635, // Андрей
            ],
            [
                "problem_type_id" => 4,
                "b_user_id" => 739, // Станислав
            ],
            [
                "problem_type_id" => 5,
                "b_user_id" => 17, // Павел
            ],
            [
                "problem_type_id" => 5,
                "b_user_id" => 198, // Артемий
            ],
            [
                "problem_type_id" => 5,
                "b_user_id" => 742, // Полина
            ],
            [
                "problem_type_id" => 5,
                "b_user_id" => 635, // Андрей
            ],
            [
                "problem_type_id" => 5,
                "b_user_id" => 739, // Станислав
            ],
            [
                "problem_type_id" => 6,
                "b_user_id" => 17, // Павел
            ],
            [
                "problem_type_id" => 6,
                "b_user_id" => 198, // Артемий
            ],
            [
                "problem_type_id" => 6,
                "b_user_id" => 739, // Станислав
            ],
        ];
        foreach ($problemTypesPerformers as $key => $value) {
            $problemTypesPerformers[$key]['created_at'] = Carbon::now();
        }
        TaskProblemTypePerformer::insert($problemTypesPerformers);

        (new TaskProblemTypeWatchersSeeder())->run();
    }
}
