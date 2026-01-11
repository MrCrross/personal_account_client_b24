<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'title' => 'Администратор к API',
                'guard_name' => 'api', // конкретный guard
            ],
            [
                'name' => 'admin',
                'title' => 'Администратор на сайте',
                'guard_name' => 'web', // конкретный guard
            ],
        ];

        $permissions = [
            [
                'name' => 'users_manage',
                'title' => 'Управление пользователями',
                'guard_name' => 'api', // для API guard
            ],
            [
                'name' => 'users_module',
                'title' => 'Раздел пользователи',
                'guard_name' => 'web', // для web guard
            ],
            [
                'name' => 'roles_manage',
                'title' => 'Управление ролями',
                'guard_name' => 'api', // для API guard
            ],
            [
                'name' => 'roles_module',
                'title' => 'Раздел ролей',
                'guard_name' => 'web', // для web guard
            ],
            [
                'name' => 'tasks_all_users',
                'title' => 'Обращения всех пользователей',
                'guard_name' => 'api', // для API guard
            ],
            [
                'name' => 'tasks_all_users',
                'title' => 'Обращения всех пользователей',
                'guard_name' => 'web', // для web guard
            ],
        ];

        $roleModels = [];
        $permissionModels = [];
// Создаем permissions
        foreach ($permissions as $permissionData) {
            $permissionModels[$permissionData['guard_name']][] = Permission::firstOrCreate(
                ['name' => $permissionData['name'], 'guard_name' => $permissionData['guard_name']],
                $permissionData
            );
        }

// Создаем roles и привязываем permissions
        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name'], 'guard_name' => $roleData['guard_name']],
                $roleData
            );
            $roleModels[$roleData['guard_name']][] = $role;
            $role->syncPermissions($permissionModels[$roleData['guard_name']]);
        }

// Назначаем роли пользователю
        $root = User::find(1);
        if ($root) {
            // Назначаем роли по их имени и guard
            $root->assignRole($roleModels['api']); // для api guard
            $root->assignRole($roleModels['web']); // для web guard
        }
    }
}
