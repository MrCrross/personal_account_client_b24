<?php

namespace App\Http\Modules\Roles\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class RolesInertiaController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('roles/RoleList', []);
    }

    /**
     * @param int $roleId
     * @return Response
     */
    public function view(int $roleId): Response
    {
        return Inertia::render('roles/RoleView', [
            'role_id' => $roleId
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('roles/RoleCreate', []);
    }

    /**
     * @param int $roleId
     * @return Response
     */
    public function edit(int $roleId): Response
    {
        return Inertia::render('roles/RoleEdit', [
            'role_id' => $roleId
        ]);
    }
}
