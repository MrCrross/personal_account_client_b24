<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    foreach ([['users_manage', 'api'], ['roles_manage', 'api'], ['users_module', 'web'], ['roles_module', 'web']] as [$name, $guard]) {
        $this->user->givePermissionTo(Permission::create(['name' => $name, 'title' => $name, 'guard_name' => $guard]));
    }
});

it('отдаёт роль с правами доступа', function () {
    $permission = Permission::create(['name' => 'tasks_all_users', 'title' => 'Все обращения', 'guard_name' => 'web']);
    $role = Role::create(['name' => 'manager', 'title' => 'Менеджер', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);

    actingAs($this->user, 'api')
        ->getJson("/api/roles/{$role->id}")
        ->assertOk()
        ->assertJsonPath('id', $role->id)
        ->assertJsonPath('title', 'Менеджер')
        ->assertJsonPath('permissions.0.name', 'tasks_all_users');
});

it('возвращает 404 для несуществующих роли и пользователя', function (string $uri) {
    actingAs($this->user, 'api')
        ->getJson($uri)
        ->assertNotFound();
})->with(['/api/roles/999999', '/api/users/999999']);

it('рендерит карточку пользователя', function () {
    actingAs($this->user)
        ->get(route('users.view', 42))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/UserView')
            ->where('userId', 42)
        );
});

it('рендерит карточку роли', function () {
    actingAs($this->user)
        ->get(route('roles.view', 7))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('roles/RoleView')
            ->where('roleId', 7)
        );
});
