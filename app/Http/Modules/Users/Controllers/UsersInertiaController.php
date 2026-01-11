<?php

namespace App\Http\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class UsersInertiaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('users/UserList', []);
    }

    public function view(int $userId): Response
    {
        return Inertia::render('users/UserView', [
            'user_id' => $userId
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('users/UserCreate', []);
    }

    public function edit(int $userId): Response
    {
        return Inertia::render('users/UserEdit', [
            'user_id' => $userId
        ]);
    }
}
