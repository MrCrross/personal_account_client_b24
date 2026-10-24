<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

// Сам список грузит связи с таблицами b_* (их нет в тестовой БД), поэтому здесь
// проверяется только валидация фильтра — до запроса к репозиторию дело не доходит
it('отклоняет некорректные параметры фильтра пользователей', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(
        Permission::create(['name' => 'users_manage', 'title' => 'Управление пользователями', 'guard_name' => 'api'])
    );

    actingAs($user, 'api')
        ->getJson('/api/users?role_id=999999&date_start=01.01.2025&per_page=0')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['role_id', 'date_start', 'per_page']);
});
