<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->givePermissionTo(
        Permission::create(['name' => 'roles_manage', 'title' => 'Управление ролями', 'guard_name' => 'api'])
    );
});

it('отдаёт роли постранично по контракту пагинации', function () {
    foreach (range(1, 3) as $i) {
        Role::create(['name' => "role_{$i}", 'title' => "Роль {$i}", 'guard_name' => 'web']);
    }

    actingAs($this->user, 'api')
        ->getJson('/api/roles?per_page=2')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('has_more', true)
        ->assertJsonPath('current_page', 1);

    actingAs($this->user, 'api')
        ->getJson('/api/roles?per_page=2&page=2')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('has_more', false)
        ->assertJsonPath('current_page', 2);
});

it('ищет роли по названию и техническому названию', function () {
    Role::create(['name' => 'manager', 'title' => 'Менеджер', 'guard_name' => 'web']);
    Role::create(['name' => 'client', 'title' => 'Клиент', 'guard_name' => 'web']);

    actingAs($this->user, 'api')
        ->getJson('/api/roles?search=Менедж')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'manager');

    actingAs($this->user, 'api')
        ->getJson('/api/roles?search=clie')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'client');
});

it('фильтрует роли по уровню ответственности и праву доступа', function () {
    $permission = Permission::create(['name' => 'users_module', 'title' => 'Пользователи', 'guard_name' => 'web']);
    $withPermission = Role::create(['name' => 'admin', 'title' => 'Админ', 'guard_name' => 'web']);
    $withPermission->givePermissionTo($permission);
    Role::create(['name' => 'web_role', 'title' => 'Страницы', 'guard_name' => 'web']);
    Role::create(['name' => 'api_role', 'title' => 'API', 'guard_name' => 'api']);

    actingAs($this->user, 'api')
        ->getJson('/api/roles?guard_name=api')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'api_role');

    actingAs($this->user, 'api')
        ->getJson("/api/roles?permission_id={$permission->id}")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'admin');
});

it('отклоняет некорректные параметры фильтра', function () {
    actingAs($this->user, 'api')
        ->getJson('/api/roles?guard_name=unknown&per_page=1000')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['guard_name', 'per_page']);
});

it('отдаёт все роли без полей пагинации', function () {
    Role::create(['name' => 'manager', 'title' => 'Менеджер', 'guard_name' => 'web']);

    actingAs($this->user, 'api')
        ->getJson('/api/roles/all')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonMissingPath('has_more');
});
