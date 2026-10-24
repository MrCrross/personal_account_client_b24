# CLAUDE.md

Гайд для агента по этому репозиторию. Только реальные конвенции проекта, без общих советов.

## Что это за проект

Личный кабинет клиента: **Laravel 13** (PHP 8.5) + **Vue 3.5** через **Inertia v2.1**. Wayfinder генерирует типизированные route-хелперы из Laravel-роутов в TS.

Frontend живёт в `resources/js/`. Backend — в `app/`. Подробные конвенции для каждой части в **нестед** `CLAUDE.md` (Claude Code сам подгружает их, когда заходишь в соответствующую подпапку):

- **`resources/js/CLAUDE.md`** — Vue/TS/Tailwind/Pinia/axios/Inertia. Frontend-конвенции.
- **`app/CLAUDE.md`** — модульная архитектура Laravel (`app/Http/Modules/<Module>/...`), интеграция с Bitrix, **префикс таблиц** и **тестовая инфраструктура** (важные подводные камни). Читать перед backend-задачей.

## Commands

**Пакетный менеджер — pnpm** (версия зафиксирована в `package.json` → `packageManager`). Не использовать npm/yarn: `package-lock.json` и `yarn.lock` в `.gitignore`, `pnpm-lock.yaml` — источник правды. Install-скрипты зависимостей pnpm по умолчанию не запускает — разрешённые перечислены в `pnpm-workspace.yaml` (`allowBuilds`). pnpm не даёт импортировать транзитивные пакеты: всё, что импортируется в коде или расширяется через `declare module` (напр. `@inertiajs/core` в `types/globals.d.ts`), должно быть в `dependencies`.

| Команда             | Когда запускать                                                      |
|---------------------|----------------------------------------------------------------------|
| `pnpm dev`          | Локальная разработка (Vite HMR на `localhost:5173`).                 |
| `pnpm build`        | Прод-сборка. Запускай перед коммитом, чтобы исключить import-ошибки. |
| `pnpm build:ssr`    | SSR-сборка (для прода, не локалки).                                  |
| `pnpm typecheck`    | `vue-tsc --noEmit`. **Обязательно** перед закрытием задачи.          |
| `pnpm lint`         | ESLint с `--fix`. **Обязательно** перед закрытием задачи.            |
| `pnpm format`       | Prettier `--write` по `resources/`.                                  |
| `pnpm format:check` | Prettier `--check` (для CI).                                         |
| `vendor/bin/pint`   | Laravel Pint (форматтер PHP) — для backend-изменений.                |
| `vendor/bin/pest`   | Pest — бэкенд-тесты. Запускай, если правил `app/` или `routes/`.     |

**Дев-сервер запускать руками не нужно** — пользователь сам решает, поднимать ли его.

### Docker (`docker/`)

Локальное окружение без PHP/Node на хосте: `docker/compose.yaml` — `mysql` (8.0, порт хоста `3307`), `app` (`php artisan serve`, `:8000`), `vite` (`pnpm dev`, `:5173`). Оба PHP-сервиса на одном образе `docker/php/Dockerfile` (PHP 8.5 + Node 22 + pnpm + composer), проект смонтирован в `/var/www`, `node_modules` — в именованном томе (linux-бинарники). В `.env` для docker: `DB_HOST=mysql`, `DB_PASSWORD=root`, `APP_URL=http://localhost:8000`.

Если на хосте нет PHP/Node, команды из таблицы выше запускать в контейнере:

- `docker compose -f docker/compose.yaml exec app vendor/bin/pest` (и `php artisan …`, `vendor/bin/pint …`)
- `docker compose -f docker/compose.yaml exec vite pnpm typecheck` (и `lint`, `build`)
- после правки `package.json`/`pnpm-lock.yaml` — `… exec vite pnpm install`

## Branches & CI

- **`develop`** — интеграционная ветка, цель для PR.
- **`main`** — продакшен. Релиз — мерж `develop → main`.
- Перед `git push`, если на дефолтной ветке — сначала feature-ветка.
- **GitHub Actions**:
  - `.github/workflows/lint.yml` — Pint + Prettier + ESLint + `pnpm typecheck`. Push/PR в `develop`/`main`.
  - `.github/workflows/tests.yml` — `pnpm build` + Pest. Push/PR в `develop`/`main`.
- **GitLab CI** (`.gitlab-ci.yml`) — деплой по push в `main` (раннер в этом репозитории пока не настроен). На сервере нужны PHP 8.5 и pnpm (см. комментарий в файле).

## Do not touch

- **Frontend генерируемое** (gitignored, Wayfinder):
  - `resources/js/actions/`
  - `resources/js/routes/`
  - `resources/js/wayfinder/`
- `vendor/`, `node_modules/`, `public/build/`, `bootstrap/ssr/`, `storage/`, `.scribe/`.
- `resources/views/mail/*` (в `.prettierignore`).

## Definition of done

Перед тем как сказать «готово»:

- [ ] `pnpm typecheck` — проходит без ошибок.
- [ ] `pnpm lint` — проходит.
- [ ] `pnpm build` — проходит.
- [ ] Если правил backend (`app/`, `routes/api/*`, `database/migrations/`, миграции/модели) — `vendor/bin/pint` отформатировал **только изменённые файлы** (не директорию — засоряет дифф); `vendor/bin/pest` — все тесты зелёные. Детали тестовой инфры — в `app/CLAUDE.md`.
- [ ] Нет `console.log`, `debugger`, закомментированных блоков кода (как в PHP, так и в TS).
- [ ] Если трогал Laravel-роут с `->name(...)` — регенерировать Wayfinder: в dev-режиме автоматически; иначе `php artisan wayfinder:generate --with-form` (флаг `--with-form` обязателен, иначе typecheck упадёт) либо `pnpm build`. См. скил `wayfinder-refresh`.
