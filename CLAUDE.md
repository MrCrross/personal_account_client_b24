# CLAUDE.md

Гайд для агента по этому репозиторию. Только реальные конвенции проекта, без общих советов.

## Что это за проект

Личный кабинет клиента: **Laravel 12** (PHP 8.4) + **Vue 3.5** через **Inertia v2.1**. Wayfinder генерирует типизированные route-хелперы из Laravel-роутов в TS.

Frontend живёт в `resources/js/`. Backend — в `app/`. Подробные конвенции для каждой части в **нестед** `CLAUDE.md` (Claude Code сам подгружает их, когда заходишь в соответствующую подпапку):

- **`resources/js/CLAUDE.md`** — Vue/TS/Tailwind/Pinia/axios/Inertia. Frontend-конвенции.
- **`app/CLAUDE.md`** — модульная архитектура Laravel (`app/Http/Modules/<Module>/...`), интеграция с Bitrix, **префикс таблиц** и **тестовая инфраструктура** (важные подводные камни). Читать перед backend-задачей.

## Commands

**Пакетный менеджер — yarn 1 (Classic)**. Не использовать npm: `package-lock.json` в `.gitignore`. `yarn.lock` — источник правды.

| Команда             | Когда запускать                                                      |
|---------------------|----------------------------------------------------------------------|
| `yarn dev`          | Локальная разработка (Vite HMR на `localhost:5173`).                 |
| `yarn build`        | Прод-сборка. Запускай перед коммитом, чтобы исключить import-ошибки. |
| `yarn build:ssr`    | SSR-сборка (для прода, не локалки).                                  |
| `yarn typecheck`    | `vue-tsc --noEmit`. **Обязательно** перед закрытием задачи.          |
| `yarn lint`         | ESLint с `--fix`. **Обязательно** перед закрытием задачи.            |
| `yarn format`       | Prettier `--write` по `resources/`.                                  |
| `yarn format:check` | Prettier `--check` (для CI).                                         |
| `vendor/bin/pint`   | Laravel Pint (форматтер PHP) — для backend-изменений.                |
| `vendor/bin/pest`   | Pest — бэкенд-тесты. Запускай, если правил `app/` или `routes/`.     |

**Дев-сервер запускать руками не нужно** — пользователь сам решает, поднимать ли его.

## Branches & CI

- **`develop`** — интеграционная ветка, цель для PR.
- **`main`** — продакшен. Релиз — мерж `develop → main`.
- Перед `git push`, если на дефолтной ветке — сначала feature-ветка.
- **GitHub Actions**:
  - `.github/workflows/lint.yml` — Pint + Prettier + ESLint + `yarn typecheck`. Push/PR в `develop`/`main`.
  - `.github/workflows/tests.yml` — `yarn build` + Pest. Push/PR в `develop`/`main`.
- **GitLab CI** (`.gitlab-ci.yml`) — деплой по push в `main`. На сервере должен быть установлен yarn 1.x (см. предупреждение в файле).

## Do not touch

- **Frontend генерируемое** (gitignored, Wayfinder):
  - `resources/js/actions/`
  - `resources/js/routes/`
  - `resources/js/wayfinder/`
- `vendor/`, `node_modules/`, `public/build/`, `bootstrap/ssr/`, `storage/`, `.scribe/`, `docker/`.
- `resources/views/mail/*` (в `.prettierignore`).

## Definition of done

Перед тем как сказать «готово»:

- [ ] `yarn typecheck` — проходит без ошибок.
- [ ] `yarn lint` — проходит.
- [ ] `yarn build` — проходит.
- [ ] Если правил backend (`app/`, `routes/api/*`, `database/migrations/`, миграции/модели) — `vendor/bin/pint` отформатировал **только изменённые файлы** (не директорию — засоряет дифф); `vendor/bin/pest` без новых падений. ⚠️ В выборке ~24 пред-существующих падения (стартеровые `Auth/*`, `ProfileUpdateTest`) — сверяй дельту «было/стало», а не «все зелёные». Детали тестовой инфры — в `app/CLAUDE.md`.
- [ ] Нет `console.log`, `debugger`, закомментированных блоков кода (как в PHP, так и в TS).
- [ ] Если трогал Laravel-роут с `->name(...)` — регенерировать Wayfinder: в dev-режиме автоматически; иначе `php artisan wayfinder:generate --with-form` (флаг `--with-form` обязателен, иначе typecheck упадёт) либо `yarn build`. См. скил `wayfinder-refresh`.
