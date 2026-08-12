<?php

namespace App\Http\Modules\Tasks\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Tasks\Services\TasksService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TasksInertiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
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
    ): Response|RedirectResponse {
        return Inertia::render('tasks/Show', [
            'taskId' => $id,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(
        int $id,
    ): Response|RedirectResponse {
        return Inertia::render('tasks/Edit', [
            'taskId' => $id,
        ]);
    }

    /**
     * Отдать содержимое вложения обращения (маркер [DISK FILE ID=nXXX]) стримом.
     * Под auth:web — грузится напрямую тегами <img>/<a> с сессионной кукой.
     */
    public function diskFile(
        int $fileId,
        TasksService $tasksService
    ): \Illuminate\Http\Response {
        $file = $tasksService->getDiskFileContent($fileId);

        if ($file === null) {
            abort(404);
        }

        return response($file['body'], 200, [
            'Content-Type' => $file['content_type'],
            'Content-Disposition' => 'inline; filename="'.str_replace('"', '', $file['name']).'"',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
