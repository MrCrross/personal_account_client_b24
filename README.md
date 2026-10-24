# Личный кабинет клиентов

Веб-версия личного кабинета для клиентов техподдержки . У клиентов нет собственных аккаунтов в Bitrix24 — весь обмен с порталом идёт от имени бота-пользователя.

**Сценарий:** клиент создаёт обращение в ЛК (тип проблемы, приоритет, описание, вложения) → в Bitrix24 от имени бота создаётся задача, файлы загружаются на Диск и прикрепляются к задаче, первый комментарий с деталями обращения пишется в задачу, в Telegram уходит уведомление о новом обращении. Дальнейшая переписка по обращению синхронизируется как комментарии задачи Bitrix24. Статус, результат и история обращения читаются из тех же данных.

**Стек:** Laravel 13 (PHP 8.5) + Vue 3.5 через Inertia v3, Wayfinder для типизированных route-хелперов. Подробные конвенции — в `CLAUDE.md` (корень, `app/CLAUDE.md`, `resources/js/CLAUDE.md`).

## Установка

Требования: PHP 8.5, Composer, Node.js 24, **pnpm** (версия — в `package.json` → `packageManager`, проще всего через `corepack enable`) — не npm/yarn (`pnpm-lock.yaml` — источник правды), MySQL. Либо только Docker — см. «Запуск в Docker» ниже.

```bash
composer install
pnpm install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

Настроить в `.env`:

- **`DB_*`** — подключение к MySQL. Важно: это та же база, где лежат собственные (нативные, без префикса) таблицы Bitrix24 — часть моделей проекта читает их напрямую, см. раздел «Bitrix24» ниже. `DB_PREFIX` — префикс таблиц самого приложения (не Bitrix24).
- **`B24_DRIVER`** — `webhook` (по умолчанию) или `database` для разработки без доступа к порталу, см. «Локальная разработка без Bitrix24» ниже.
- **`B24_WEBHOOK`** — входящий вебхук Bitrix24, см. необходимые права ниже.
- **`B24_DOMAIN`** — публичный адрес портала Bitrix24 (используется в ссылках внутри уведомлений и комментариев к задаче).
- **`TELEGRAM_NOTIFY_HOOK_URL`** / **`TELEGRAM_NOTIFY_HOOK_KEY`** — вебхук телеграм-бота для уведомления о новом обращении. Необязательно для локальной разработки: ошибка отправки перехватывается и не блокирует создание обращения (`TasksService::store`).

Далее:

```bash
php artisan migrate --seed   # схема + справочники (статусы, типы проблем, права) + тестовый пользователь
pnpm dev                      # либо `composer run dev` — поднимает serve + queue:listen + vite разом
```

Сидер (`DatabaseSeeder`) создаёт тестового пользователя `root@root.com` / `password`. Дев-сервер запускать нужно только если сам этого хочешь — большинство задач можно решить без него.

Полный список pnpm/composer-команд (typecheck, lint, build, pint, pest) — в корневом `CLAUDE.md`.

### Юридические страницы (обязательно перед запуском)

Тексты политик лежат в репозитории с **заглушками вместо реквизитов** — перед выкладкой их нужно заполнить данными своей компании (оператора персональных данных):

- `resources/js/pages/policies/Confidential.vue` — политика конфиденциальности (`/policies/confidential`);
- `resources/js/pages/policies/Cookies.vue` — согласие на использование cookie (`/policies/cookies`).

Заменить все плейсхолдеры в квадратных скобках — `[НАЗВАНИЕ КОМПАНИИ]`, `[ИНН]`, `[ОГРН]`, `[ИНДЕКС]`, `[РЕГИОН]`, `[ГОРОД]`, `[УЛИЦА, ДОМ]` — и адрес сайта `https://example.com/`. Проверить, что ничего не осталось: `grep -rn "\[НАЗВАНИЕ\|\[ИНН\]\|\[ОГРН\]\|\[ИНДЕКС\]\|\[РЕГИОН\]\|\[ГОРОД\]\|\[УЛИЦА\|example.com" resources/js/pages/policies`. Сам текст политик — типовой; его соответствие 152-ФЗ и фактическим процессам компании стоит согласовать с юристом.

### Запуск в Docker

PHP и Node на хосте не нужны: `docker/compose.yaml` поднимает MySQL 8.0, приложение (`php artisan serve`) и Vite с HMR на одном образе PHP 8.5 + Node 24 + pnpm (`docker/php/Dockerfile`).

```bash
cp .env.example .env
# в .env: APP_URL=http://localhost:8000, DB_HOST=mysql, DB_PASSWORD=root, B24_DRIVER=database (если нет доступа к Bitrix24)
docker compose -f docker/compose.yaml build
docker compose -f docker/compose.yaml run --rm --no-deps app composer install
docker compose -f docker/compose.yaml run --rm --no-deps vite pnpm install
docker compose -f docker/compose.yaml run --rm app sh -c "php artisan key:generate && php artisan jwt:secret --force && php artisan migrate --seed"
docker compose -f docker/compose.yaml up -d
```

- Приложение — http://localhost:8000, Vite — `:5173`, MySQL снаружи — `localhost:3307` (`root` / `root`, база `ff_ts_lk`).
- Команды — внутри контейнеров: `docker compose -f docker/compose.yaml exec app vendor/bin/pest`, `… exec vite pnpm typecheck` и т.д.
- `node_modules` лежит в именованном томе (нужны linux-бинарники esbuild/rolldown/tailwind), поэтому `pnpm install` выполнять в контейнере `vite`, а не на хосте.

## Bitrix24

Интеграция с Bitrix24 состоит из двух независимых частей — их легко перепутать при настройке нового окружения.

### 1. Входящий вебхук (`B24_WEBHOOK`)

REST-вызовы к порталу (`app/Http/Wrappers/BWebHookConstructor.php`, маппинг методов — `config/b24.php`). У входящего вебхука должны быть включены права:

| Право (как называется в Bitrix24)     | Зачем нужно                                                             |
|----------------------------------------|--------------------------------------------------------------------------|
| **CRM** (`crm`)                        | `crm.contact.add` — создание контакта клиента при первом обращении.      |
| **Задачи** (`tasks`, новое REST API)    | `tasks.task.add`, `tasks.task.files.attach`, `tasks.task.update` — создание задачи, прикрепление файлов, редактирование обращения клиентом. |
| **Задачи** (`task`, старое REST API)    | `task.commentitem.add` — первый и последующие комментарии в задаче от бота. |
| **Диск** (`disk`)                       | `disk.storage.uploadfile` (загрузка вложений), `disk.attachedObject.get` / `disk.file.get` (получение ссылки на скачивание). |

`config/b24.php` также содержит несколько заранее замапленных, но пока не используемых кодом методов (`crm.contact.list/get`, `tasks.task.list/get`, `tasks.task.result.list`, `sonet_group.get`) — держать соответствующие права включёнными не обязательно, но не помешает про запас.

В `config/b24.php` также нужно настроить автоматически добавляемых наблюдателей (`default.auditors`), исполнителей (`default.responsible`), а также от кого будут создаваться задачи (`createdByUser`)

### 2. Прямое подключение к БД Bitrix24 (`DB_*`)

Часть данных (`app/Models/Bitrix24/**` — read-only «модели-зеркала», обычные Eloquent-модели без своего префикса) читается не через REST, а прямым SQL-запросом к нативным (без префикса) таблицам Bitrix24 в той же базе: `b_user`, `b_sonet_group`, `b_task`, `b_tasks_result`, `b_iblock*` и т.п. Это отдельный от вебхука механизм — правами вебхука он не регулируется, для него важно только то, что MySQL-пользователь из `DB_*` имеет `SELECT`-доступ к базе, где Bitrix24 хранит свои таблицы. Из Bitrix24 мы **читаем результаты задач**, но не читаем переписку/чат — туда только пишем комментарии через вебхук.

### Локальная разработка без Bitrix24

Если доступа к порталу и его БД нет:

1. `php artisan migrate --seed` — миграция `create_local_bitrix_mirror_tables` создаёт недостающие таблицы `b_*` (минимальный набор колонок, который нужен коду), а `TaskTestSeeder` при `APP_DEBUG=true` заполняет их тестовыми данными (пользователи, группа, компании, задачи, результат). Раньше для этого нужен был `public/sql/b_tables_create.sql` — больше не нужен. В `production` и `testing` миграция пропускается, существующие таблицы не трогает, откат ничего не удаляет.
2. `B24_DRIVER=database` в `.env` — вместо REST-вызовов через вебхук `App\Http\Wrappers\BLocalRest` пишет задачи, контакты, комментарии и файлы Диска прямо в локальные таблицы `b_*` (файлы — в `storage/app/private/b24-local/disk`). Создание обращения, переписка и вложения работают как с порталом. В `production` этот режим запрещён.

Уведомление в Telegram в этом режиме по-прежнему уходит на `TELEGRAM_NOTIFY_HOOK_URL`; если он недоступен, ошибка только логируется.
