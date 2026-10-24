<?php

use App\Http\Modules\Tasks\Enums\TaskStatusesEnum;
use App\Models\Bitrix24\Task\BTask;
use App\Models\Tasks\Task;
use App\Models\Tasks\TaskPriority;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\patchJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['b24.webhook' => 'https://bitrix.test/rest/1/token/']);

    // Зеркала b_* в тестовой БД нет (миграция пропускается в testing) — сервис читает
    // из b_tasks только статус и описание, поэтому создаём минимальную таблицу сами
    Schema::create('b_tasks', function (Blueprint $table) {
        $table->increments('ID');
        $table->string('TITLE')->nullable();
        $table->mediumText('DESCRIPTION')->nullable();
        $table->char('PRIORITY', 1)->default('1');
        $table->char('STATUS', 1)->nullable();
    });

    TaskPriority::query()->insert([
        ['b_priority' => 0, 'name' => 'Низкий'],
        ['b_priority' => 1, 'name' => 'Средний'],
        ['b_priority' => 2, 'name' => 'Высокий'],
    ]);

    $this->makeTask = function (User $creator, array $bTaskFields = []): Task {
        $bTaskId = BTask::query()->insertGetId([
            'TITLE' => 'Старое название',
            'DESCRIPTION' => 'Старое описание',
            'PRIORITY' => 1,
            'STATUS' => TaskStatusesEnum::ACTIVE->value,
            ...$bTaskFields,
        ]);

        return Task::factory()->create(['user_creator_id' => $creator->id, 'b_task_id' => $bTaskId]);
    };

    $this->payload = [
        'name' => 'Новое название',
        'description' => '[P]Новое описание[/P]',
        'priority_id' => 2,
    ];
});

it('создатель меняет название, описание и приоритет — изменения уходят в задачу Битрикса', function () {
    Http::fake(['*' => Http::response(['result' => ['task' => ['id' => 1]]], 200)]);

    $user = User::factory()->create();
    $task = ($this->makeTask)($user);

    actingAs($user, 'api')
        ->patchJson("/api/tasks/{$task->id}", $this->payload)
        ->assertNoContent();

    Http::assertSent(function ($request) use ($task) {
        return str_contains($request->url(), 'tasks.task.update')
            && (int) $request['taskId'] === $task->b_task_id
            && $request['fields']['TITLE'] === 'Новое название'
            && $request['fields']['DESCRIPTION'] === '[P]Новое описание[/P]'
            && (int) $request['fields']['PRIORITY'] === 2;
    });
});

it('сохраняет маркеры вложений Диска из прежнего описания', function () {
    Http::fake(['*' => Http::response(['result' => ['task' => ['id' => 1]]], 200)]);

    $user = User::factory()->create();
    $task = ($this->makeTask)($user, [
        'DESCRIPTION' => "Старое описание\n[DISK FILE ID=n101]\n[DISK FILE ID=202]",
    ]);

    actingAs($user, 'api')
        ->patchJson("/api/tasks/{$task->id}", $this->payload)
        ->assertNoContent();

    Http::assertSent(fn ($request) => $request['fields']['DESCRIPTION']
        === "[P]Новое описание[/P]\n[DISK FILE ID=n101]\n[DISK FILE ID=202]");
});

it('не даёт изменить чужое обращение, даже администратору', function () {
    Http::fake();

    $admin = User::factory()->create();
    $admin->givePermissionTo(
        Permission::create(['name' => 'tasks_all_users', 'title' => 'Все обращения', 'guard_name' => 'api'])
    );
    $task = ($this->makeTask)(User::factory()->create());

    actingAs($admin, 'api')
        ->patchJson("/api/tasks/{$task->id}", $this->payload)
        ->assertNotFound();

    Http::assertNothingSent();
});

it('не даёт изменить завершённое обращение', function () {
    Http::fake();

    $user = User::factory()->create();
    $task = ($this->makeTask)($user, ['STATUS' => TaskStatusesEnum::COMPLETED->value]);

    actingAs($user, 'api')
        ->patchJson("/api/tasks/{$task->id}", $this->payload)
        ->assertUnprocessable()
        ->assertJsonPath('error', true);

    Http::assertNothingSent();
});

it('возвращает 502, если Битрикс недоступен', function () {
    Http::fake(function () {
        throw new ConnectionException('Bitrix is down');
    });

    $user = User::factory()->create();
    $task = ($this->makeTask)($user);

    actingAs($user, 'api')
        ->patchJson("/api/tasks/{$task->id}", $this->payload)
        ->assertStatus(502)
        ->assertJsonPath('error', true);
});

it('возвращает 502, если Битрикс ответил ошибкой', function () {
    Http::fake(['*' => Http::response(['error' => 'ERROR_CORE', 'error_description' => 'Access denied'], 400)]);

    $user = User::factory()->create();
    $task = ($this->makeTask)($user);

    actingAs($user, 'api')
        ->patchJson("/api/tasks/{$task->id}", $this->payload)
        ->assertStatus(502);
});

it('валидирует поля', function () {
    Http::fake();

    $user = User::factory()->create();
    $task = ($this->makeTask)($user);

    actingAs($user, 'api')
        ->patchJson("/api/tasks/{$task->id}", ['name' => '', 'priority_id' => 99])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'description', 'priority_id']);

    Http::assertNothingSent();
});

it('требует аутентификацию', function () {
    $task = ($this->makeTask)(User::factory()->create());

    patchJson("/api/tasks/{$task->id}", $this->payload)
        ->assertUnauthorized();
});
