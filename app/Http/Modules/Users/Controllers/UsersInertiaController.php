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

    public function view(int $id): Response
    {
        return Inertia::render('users/UserView', [
            'userId' => $id,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('users/UserCreate', []);
    }
}
