<?php

namespace App\Http\Modules\Roles\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class RolesInertiaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('roles/RoleList', []);
    }

    public function view(int $id): Response
    {
        return Inertia::render('roles/RoleView', [
            'roleId' => $id,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('roles/RoleCreate', []);
    }
}
