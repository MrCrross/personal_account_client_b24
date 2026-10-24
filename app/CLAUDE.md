# Backend (app/) — гайд

Гайд по бэкенду. Подгружается при работе в `app/`. Общее — в корневом `CLAUDE.md`, фронт — в `resources/js/CLAUDE.md`. Пути от корня репозитория.

## Stack

- **Laravel 13** / **PHP 8.5**
- **Inertia** v3 (рендер страниц через `Inertia::render`)
- **JWT** (`php-open-source-saver/jwt-auth`) — API-гард `auth:api`
- **Spatie Permission** — роли/права (`$user->can('...')`)
- **Scribe** — генерация API-доков из докблоков (`@group`, `@response`, см. скил `scribe-docblock-helper`)
- **Pest** — тесты (см. скил `pest-test-author`)
- **Pint** — форматтер

## Модульная архитектура

Каждый домен — модуль в `app/Http/Modules/<Module>/`. Существующие модули: **Auth, Bitrix, Notifications, Roles, Settings, Tasks, Users**.

```
app/Http/Modules/<Module>/
  Controllers/    — тонкие; разрешают доступ, дергают сервис, возвращают JsonResponse/Resource
  Services/       — оркестрация бизнес-логики; собирают данные из репозиториев и внешних сервисов
  Repositories/   — доступ к БД. Методы СТАТИЧЕСКИЕ, возвращают Eloquent-модели/Paginator
  Resources/      — JsonResource/ResourceCollection; форматируют ответ
  Requests/       — FormRequest: валидация (authorize() обычно true, доступ решает контроллер)
  Enums/          — backed-энумы (напр. TaskStatusesEnum: int)
  Dictionaries/   — статические массивы (тексты валидации, маппинги)
```

Не каждый модуль имеет все папки (напр. `Bitrix` — только Services/Repositories, без своих роутов).

### Конвенции слоёв

- **Контроллеры**: разрешение доступа здесь, не в Request. Паттерн: `if (Auth::user()->can('tasks_all_users')) { ...всё... } else { ...только свои... }`. Возвращают `response()->json($resource)`.
- **Сервисы**: инстанцируются как `new TasksService` (DI в контроллере через type-hint тоже работает). Внешние вызовы (Bitrix) — внутри сервиса. Возвращают Resource или `null`.
- **Репозитории**: класс с **статическими** методами (`TasksRepository::getById($id)`). Сырые вставки — через `insertGetId($fields)` (обходит `$fillable`, см. ниже). Выборки — `select(...)->with([...])`. Пагинация — `simplePaginate()`.
- **Resources**: `toArray()` возвращает `(object)[...]` (каст к объекту — осознанный стиль проекта, не массив). Для связей, которые грузятся НЕ всегда, — guard `$this->relationLoaded('rel') ? ... : []`, иначе N+1 или lazy-load в списках.

### Контракт пагинации

`*PaginateCollection` возвращают `{ data, has_more, current_page }` (`has_more` = `hasMorePages()`). Фронт завязан на эти поля — новые пагинированные ресурсы держат тот же контракт.

## Роуты

- Разбиты по модулям: `routes/api/<module>.php`, `routes/web/<module>.php`.
- Подключаются группами из `routes/api.php` (всё под `auth:api`, кроме `auth`) и `routes/web.php`.
- **Именованные роуты обязательны** для использования во фронте (Wayfinder генерирует TS-хелперы только из `->name(...)`). После добавления роута — регенерация Wayfinder, см. корневой `CLAUDE.md` и скил `wayfinder-refresh` (**команда `php artisan wayfinder:generate --with-form`**).
- Inertia-страницы рендерятся из `*InertiaController` (`Inertia::render('module/Page', [...props])`). Данные страница обычно тянет сама через API (props передают только id).

## ⚠️ Префикс таблиц — НЕОЧЕВИДНО

`config/database.php`: у самих соединений `'prefix' => ''` (пусто!), а верхнеуровневый `'prefix' => env('DB_PREFIX')` (= `ff_ts_lk_`) — **кастомное значение, которое проект применяет ВРУЧНУЮ**:

- **Миграции**: `Schema::create(config('database.prefix') . 'tasks', ...)` → создаёт `ff_ts_lk_tasks` буквально.
- **Модели**: `PrefixedModel` (и `User`) в конструкторе дописывают префикс к `$this->table`. В модели поле объявляется БЕЗ префикса: `public $table = 'tasks';` → в рантайме `ff_ts_lk_tasks`.
- **Query builder НЕ префиксует** (prefix соединения пустой). Поэтому `DB::table('tasks')` / `assertDatabaseHas('tasks', ...)` ищут НЕпрефиксную `tasks` → «no such table».

**Следствия:**
- Новая модель — наследует `PrefixedModel`, `$table` без префикса.
- Новая миграция — `Schema::create(config('database.prefix') . '<table>', ...)` (см. любую существующую).
- Сырой доступ к таблице (тесты, `DB::table`) — брать имя через `(new Model)->getTable()`, не строковый литерал.

### `$fillable` и сырые вставки

У `Task` в `$fillable` НЕ все колонки (напр. нет `problem_type_id`). Поэтому `TasksRepository::store()` пишет через `insertGetId($fields)` (обход mass-assignment). При новых полях либо добавляй в `$fillable`, либо используй сырую вставку осознанно. (Фабрики Eloquent `$fillable` игнорируют — пишут через `Model::unguarded`.)

## Интеграция с Bitrix24

Внешний Bitrix — через REST-вебхук. Из Bitrix мы **читаем результаты** задач (модели-зеркала `b_*`, связь `Task::bResult`), но **переписку/чат не читаем** — только пишем туда комментарии.

- **`app/Http/Wrappers/BWebHookConstructor.php`** — база. URL из `config('b24.webhook')` (env `B24_WEBHOOK`). Методы: `setRoute()`, `addFields()`, `setBodyData()`, `execute()`. `execute()` возвращает `$response->json()` при успехе, иначе сам `Response`.
- **`config/b24.php`** — маппинг логических путей на REST-методы (`tasks.create` → `tasks.task.add`, `tasks.comments.add` → `task.commentitem.add`, ...).
- **Bot-пользователь** (`config('b24.createdByUser')`, env `B24_CREATED_BY_USER`; на бою — `312`) — от его имени создаются задачи/комментарии (у клиентов ЛК нет своих Bitrix-аккаунтов). Сообщения клиента уходят комментарием от бота с префиксом ФИО клиента.
- **`b_*` модели** (`app/Models/Bitrix24/`) — read-only зеркала таблиц Bitrix, без префикса проекта. На бою это настоящие таблицы портала в той же БД. Для локальной разработки их создаёт миграция `create_local_bitrix_mirror_tables` (только недостающие таблицы, минимальный набор колонок; в `production`/`testing` пропускается через `shouldRun()`, `down()` ничего не удаляет), а наполняет `TaskTestSeeder` (при `APP_DEBUG`, если `b_user` пуст). Старый `public/sql/b_tables_create.sql` больше не используется. В тестах с `RefreshDatabase` таблиц `b_*` нет — код, который их трогает, надо мокать/обходить.
- **Драйвер `B24_DRIVER`** (`config('b24.driver')`): `webhook` (по умолчанию) — REST через вебхук; `database` — без вебхука, для разработки без доступа к порталу. Переключение — в `BWebHookConstructor::execute()`/`download()`, сервисы модуля Bitrix о драйвере не знают. `App\Http\Wrappers\BLocalRest` эмулирует используемые REST-методы (`tasks.task.add`, `tasks.task.update`, `tasks.task.files.attach`, `task.commentitem.add`, `crm.contact.add`, `disk.storage.uploadfile`, `disk.attachedObject.get`, `disk.file.get`) записью/чтением `b_*`, ответы — в форме REST Bitrix24; файлы Диска — на диске `local` в `b24-local/disk/<ID>/<имя>`. **Новый REST-вызов → добавить его эмуляцию в `BLocalRest::call()`**, иначе в `database`-режиме будет `RuntimeException`. В `production` драйвер `database` бросает исключение. В тестах драйвер всегда `webhook` — его выставляет `tests/TestCase::setUp()` (тесты работают через `Http::fake()`).
- **Resilience**: внешний вызов оборачивай в `try/catch (\Throwable)` + `report()`, чтобы падение Bitrix не теряло локальные данные. Пример — `TasksService::addMessage()`: сначала пытаемся отправить в Bitrix, при ошибке `b_comment_id = null`, но локально сообщение всё равно сохраняется.

## Уведомления (`Notifications`)

- **`TelegramNotifyService::notifyTaskCreated()`** — уведомление в Telegram при создании задачи (`TasksService::store`). URL/ключ хука — `config('telegram.notify.hook_url'/'hook_key')` (env `TELEGRAM_NOTIFY_HOOK_URL`/`TELEGRAM_NOTIFY_HOOK_KEY`).
- Вызов обёрнут в `try/catch` + `report()` в `TasksService::store` — тот же resilience-паттерн, что и для Bitrix: недоступность Telegram не должна ронять создание обращения.

## Команды и Definition of Done

- `vendor/bin/pint <изменённые файлы>` — **только по своим файлам**. `pint <директория>` переформатирует чужие файлы и засоряет дифф.
- `vendor/bin/pest` — если правил `app/`/`routes/`/миграции. См. скил и раздел ниже про тестовую инфраструктуру.
- Док-блоки Scribe для новых эндпоинтов — скил `scribe-docblock-helper`.

## ⚠️ Тестовая инфраструктура (читать до запуска Pest)

- Тесты идут в **`sqlite :memory:`** (phpunit.xml `<env>` переопределяет `.env` с MySQL). Проверено диагностикой `DB::connection()->getDatabaseName()`.
- **`RefreshDatabase` ВЫКЛЮЧЕН глобально** (закомментирован в `tests/Pest.php`) → схемы в `:memory:` нет. Включай **пофайлово**: `uses(RefreshDatabase::class)` в своём тест-файле.
- **Все тесты зелёные** — так и держать. Регистрации, сброса/подтверждения пароля и верификации email в проекте нет (контроллеры и роуты удалены, колонки `email_verified_at` нет) — тестов на них тоже нет. Логин (`login.store`) отвечает JSON с JWT и открывает web-сессию.
- **Без захардкоженных ID окружения**: ID бота/ответственного и т.п. тест задаёт сам через `config([...])` и сверяется с этим значением, а не с числом из `.env`/прода. Фабрики не завязаны на конкретные ID: `UserFactory` даёт случайные `b_group_id`/`b_company_id`, `TaskFactory` создаёт тип проблемы через `TaskProblemTypeFactory`. Нужные значения передавай в `create([...])` явно.
- **Никогда не включай `RefreshDatabase` глобально, не убедившись, что подключение `:memory:`** — на реальном MySQL `ff_ts_lk` это `migrate:fresh` и потеря dev-данных.
- Детальные паттерны (префикс в `assertDatabaseHas`, `actingAs($user, 'api')`, `Http::fake()` для Bitrix, фабрики) — в скиле `pest-test-author`.
