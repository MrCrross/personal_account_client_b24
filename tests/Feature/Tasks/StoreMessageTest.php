<?php

use App\Models\Tasks\Task;
use App\Models\Tasks\TaskMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Стабильный URL вебхука, чтобы запрос к Битриксу формировался корректно
    // ID бота в Битриксе зависит от окружения — задаём свой, а не берём из .env
    $this->botUserId = fake()->numberBetween(1, 99999);
    config([
        'b24.webhook' => 'https://bitrix.test/rest/1/token/',
        'b24.createdByUser' => $this->botUserId,
    ]);
    // Префикс таблиц проект добавляет вручную в моделях, у соединения он пустой —
    // поэтому в assertDatabase* передаём фактическое имя таблицы
    $this->messagesTable = (new TaskMessage)->getTable();
});

it('сохраняет сообщение локально и отправляет комментарий в задачу Битрикса', function () {
    Http::fake([
        '*' => Http::response(['result' => 777], 200),
    ]);

    $user = User::factory()->create();
    $task = Task::factory()->create(['user_creator_id' => $user->id, 'b_task_id' => 4321]);

    actingAs($user, 'api')
        ->postJson("/api/tasks/{$task->id}/messages", ['text' => "Здравствуйте,\nкогда будет готово?"])
        ->assertOk()
        ->assertJsonStructure(['id', 'text', 'author' => ['id', 'name'], 'created_at'])
        ->assertJsonPath('text', "Здравствуйте,\nкогда будет готово?")
        ->assertJsonPath('author.id', $user->id);

    $this->assertDatabaseHas($this->messagesTable, [
        'task_id' => $task->id,
        'user_id' => $user->id,
        'text' => "Здравствуйте,\nкогда будет готово?",
        'b_comment_id' => 777,
    ]);
});

it('отправляет в Битрикс комментарий от бота с префиксом имени клиента', function () {
    Http::fake(['*' => Http::response(['result' => 1], 200)]);

    $user = User::factory()->create(['name' => 'Пётр Клиентов']);
    $task = Task::factory()->create(['user_creator_id' => $user->id]);

    actingAs($user, 'api')
        ->postJson("/api/tasks/{$task->id}/messages", ['text' => 'Тестовое сообщение'])
        ->assertOk();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'task.commentitem.add')
            && (int) $request['FIELDS']['AUTHOR_ID'] === $this->botUserId
            && str_contains($request['FIELDS']['POST_MESSAGE'], 'Сообщение от Пётр Клиентов')
            && str_contains($request['FIELDS']['POST_MESSAGE'], 'Тестовое сообщение');
    });
});

it('сохраняет сообщение локально, даже если Битрикс недоступен', function () {
    Http::fake(function () {
        throw new ConnectionException('Bitrix is down');
    });

    $user = User::factory()->create();
    $task = Task::factory()->create(['user_creator_id' => $user->id]);

    actingAs($user, 'api')
        ->postJson("/api/tasks/{$task->id}/messages", ['text' => 'Сообщение без Битрикса'])
        ->assertOk()
        ->assertJsonPath('text', 'Сообщение без Битрикса');

    $this->assertDatabaseHas($this->messagesTable, [
        'task_id' => $task->id,
        'text' => 'Сообщение без Битрикса',
        'b_comment_id' => null,
    ]);
});

it('возвращает 404 для обращения другого пользователя и не сохраняет сообщение', function () {
    Http::fake();

    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $task = Task::factory()->create(['user_creator_id' => $owner->id]);

    actingAs($stranger, 'api')
        ->postJson("/api/tasks/{$task->id}/messages", ['text' => 'Чужое сообщение'])
        ->assertNotFound();

    $this->assertDatabaseMissing($this->messagesTable, ['task_id' => $task->id]);
    Http::assertNothingSent();
});

it('валидирует обязательное поле text', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_creator_id' => $user->id]);

    actingAs($user, 'api')
        ->postJson("/api/tasks/{$task->id}/messages", [])
        ->assertStatus(422)
        ->assertJsonValidationErrors('text');
});

it('загружает вложения в Битрикс-Диск, прикрепляет их к комментарию и резолвит в ответе', function () {
    Http::fake([
        '*disk.storage.uploadfile*' => Http::response(['result' => ['ID' => 555]], 200),
        '*task.commentitem.add*' => Http::response(['result' => 888], 200),
        '*disk.attachedObject.get*' => Http::response(['result' => [
            'NAME' => 'shot.png',
            'SIZE' => 1024,
            'DOWNLOAD_URL' => 'https://bitrix.test/download/shot.png?token=x',
        ]], 200),
    ]);

    $user = User::factory()->create();
    $task = Task::factory()->create(['user_creator_id' => $user->id, 'b_task_id' => 4321]);

    actingAs($user, 'api')
        ->post("/api/tasks/{$task->id}/messages", [
            'text' => 'Смотрите скрин',
            'files' => [UploadedFile::fake()->image('shot.png')],
        ])
        ->assertOk()
        ->assertJsonPath('files.0.id', 555)
        ->assertJsonPath('files.0.is_image', true);

    // Локально храним только ID вложения, контент не дублируем
    $message = TaskMessage::query()->where('task_id', $task->id)->first();
    expect($message->b_disk_file_ids)->toBe([555]);

    // Файл ушёл в Диск и прикреплён к комментарию как n555
    Http::assertSent(fn ($request) => str_contains($request->url(), 'disk.storage.uploadfile'));
    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'task.commentitem.add')
            && $request['FIELDS']['UF_FORUM_MESSAGE_DOC'] === ['n555'];
    });
});

it('разрешает сообщение только с вложениями, без текста', function () {
    Http::fake([
        '*disk.storage.uploadfile*' => Http::response(['result' => ['ID' => 42]], 200),
        '*task.commentitem.add*' => Http::response(['result' => 1], 200),
        '*disk.attachedObject.get*' => Http::response(['result' => [
            'NAME' => 'doc.pdf',
            'SIZE' => 10,
            'DOWNLOAD_URL' => 'https://bitrix.test/download/doc.pdf?token=y',
        ]], 200),
    ]);

    $user = User::factory()->create();
    $task = Task::factory()->create(['user_creator_id' => $user->id]);

    actingAs($user, 'api')
        ->post("/api/tasks/{$task->id}/messages", [
            'files' => [UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf')],
        ])
        ->assertOk()
        ->assertJsonPath('files.0.id', 42)
        ->assertJsonPath('files.0.is_image', false);

    $this->assertDatabaseHas($this->messagesTable, [
        'task_id' => $task->id,
        'text' => '',
    ]);
});

it('требует аутентификацию', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_creator_id' => $user->id]);

    postJson("/api/tasks/{$task->id}/messages", ['text' => 'Аноним'])
        ->assertUnauthorized();
});
