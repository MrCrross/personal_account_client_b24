<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected from home to the login page', function () {
    $this->get(route('home'))->assertRedirect(route('login'));
});

test('authenticated users are redirected from home to the tasks list', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertRedirect(route('tasks.list'));
});
