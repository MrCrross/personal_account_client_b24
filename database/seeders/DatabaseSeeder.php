<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Tasks\TaskProblemTypesSeeder;
use Database\Seeders\Tasks\TaskStatusSeeder;
use Database\Seeders\Tasks\TaskTestSeeder;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'root',
            'email' => 'root@root.com',
            // Группа и компания из тестовых данных TaskTestSeeder (b_sonet_group / b_crm_company)
            'b_group_id' => 12,
            'b_company_id' => 3943,
        ]);
        $calls = [
            TaskStatusSeeder::class,
            TaskProblemTypesSeeder::class,
            PermissionSeeder::class,
        ];
        if (config('app.debug')) {
            $calls[] = TaskTestSeeder::class;
            $calls[] = UsersTestSeeder::class;
        }
        $this->call($calls);
    }
}
