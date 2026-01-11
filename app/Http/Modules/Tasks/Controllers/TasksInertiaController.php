<?php

namespace App\Http\Modules\Tasks\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TasksInertiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response {
        return Inertia::render('tasks/TaskList', []);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('tasks/TaskCreate', []);
    }

    /**
     * Display the specified resource.
     */
    public function show(
        int $id,
    ): Response | RedirectResponse {
        return Inertia::render('tasks/Show', [
            'taskId' => $id,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(
        int $id,
    ): Response | RedirectResponse {
        return Inertia::render('tasks/Edit', [
            'taskId' => $id,
        ]);
    }

}
