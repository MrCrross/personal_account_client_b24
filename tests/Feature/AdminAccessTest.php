<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

function grantPermission(User $user, string $name, string $guard): void
{
    $user->givePermissionTo(Permission::create(['name' => $name, 'title' => $name, 'guard_name' => $guard]));
}

it('запрещает API пользователей и ролей без прав', function (string $method, string $uri) {
    actingAs($this->user, 'api')
        ->json($method, $uri)
        ->assertForbidden();
})->with([
    ['GET', '/api/users'],
    ['POST', '/api/users'],
    ['PATCH', '/api/users/1'],
    ['GET', '/api/roles'],
    ['GET', '/api/roles/all'],
    ['GET', '/api/roles/permissions'],
    ['POST', '/api/roles'],
    ['PATCH', '/api/roles/1'],
]);

it('не пускает с правом web-guard в API', function () {
    grantPermission($this->user, 'roles_manage', 'web');

    actingAs($this->user, 'api')
        ->getJson('/api/roles')
        ->assertForbidden();
});

it('с правом users_manage отдаёт полный список ролей, но не управление ролями', function () {
    grantPermission($this->user, 'users_manage', 'api');

    actingAs($this->user, 'api')
        ->getJson('/api/roles/all')
        ->assertOk();

    actingAs($this->user, 'api')
        ->getJson('/api/roles')
        ->assertForbidden();
});

it('с правом roles_manage не пускает в API пользователей', function () {
    grantPermission($this->user, 'roles_manage', 'api');

    actingAs($this->user, 'api')
        ->getJson('/api/users')
        ->assertForbidden();
});

it('запрещает страницу администрирования без прав на разделы', function () {
    actingAs($this->user)
        ->get(route('admin'))
        ->assertForbidden();
});

it('открывает страницу администрирования с правом на любой раздел', function (string $permission) {
    grantPermission($this->user, $permission, 'web');

    actingAs($this->user)
        ->get(route('admin'))
        ->assertOk();
})->with(['users_module', 'roles_module']);
