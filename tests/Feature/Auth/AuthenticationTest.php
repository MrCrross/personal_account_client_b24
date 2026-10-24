<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->postJson(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    // Вход открывает web-сессию и выдаёт JWT для API
    $response->assertOk()
        ->assertJsonPath('error', false)
        ->assertJsonPath('data.user.id', $user->id)
        ->assertJsonStructure(['data' => ['access_token', 'token_type', 'expires_on']]);
    $this->assertAuthenticatedAs($user, 'web');
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->postJson(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertUnprocessable()->assertJsonValidationErrors('email');

    $this->assertGuest('web');
});

test('users can logout', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $this->actingAs($user, 'web')
        ->withToken($token)
        ->post(route('logout'))
        ->assertOk();

    $this->assertGuest('web');
});

test('users are rate limited', function () {
    $user = User::factory()->create();

    $throttleKey = Str::transliterate(Str::lower($user->email).'|127.0.0.1');
    RateLimiter::increment($throttleKey, amount: 10);

    $this->postJson(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email' => 'Вы заблокированы за множественные попытки входа.']);

    $this->assertGuest('web');
});
