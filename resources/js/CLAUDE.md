# Frontend (resources/js) — гайд

Гайд для агента по фронтенду. Этот файл подгружается, когда работаем в `resources/js/`. Общие сведения о проекте — в корневом `CLAUDE.md`. Пути ниже — от корня репозитория, если не указано иначе.

## Stack

- **Vue** 3.5.13 — `<script setup lang="ts">` везде
- **TypeScript** 5.2 — `strict: true`
- **Vite** 7 (плагины: `@vitejs/plugin-vue`, `@tailwindcss/vite`, `laravel-vite-plugin`, `@laravel/vite-plugin-wayfinder`)
- **Tailwind CSS** 4.1 — `@theme inline` в CSS, без `tailwind.config.js`
- **Pinia** 3 (setup-стиль)
- **Inertia.js** v2.1 (`@inertiajs/vue3`) поверх Laravel
- **Laravel Wayfinder** — генерация типизированных route-хелперов из Laravel-роутов
- **shadcn-vue** UI (конфиг — `components.json`)
- **reka-ui** 2.2 — headless-примитивы под капотом shadcn-vue
- **Tiptap** 3 — Editor
- **VueUse** 12 — composables
- **axios** 1.11
- **lucide-vue-next** — иконки
- **vs-vue3-select** — селекты (типы прошиты shim'ом, см. ниже)
- **Tooling**: ESLint 9 flat-config, Prettier 3, vue-tsc 2.2

## Структура

```
resources/js/
  actions/      — генерируется Wayfinder, gitignored, НЕ ТРОГАТЬ
  components/
    ui/           — кастомные UI-компоненты поверх reka-ui (в основном модифицированные из laravel vue-starter, плюс свои). ESLint+Prettier игнорят их по конфигу — править можно и нужно, но придерживайся существующих паттернов.
    entities/     — компоненты, привязанные к доменным сущностям (roles, tasks, users)
    forms/        — формы (TaskCreateForm, RoleForm, TaskListFilter и т.п.)
    shared/       — приложение-уровень (app, menu, filters/PerPageSelector)
  composables/  — Vue composables (useToast, useDateFormat, useBBCode, usePerPage, useAppearance)
  features/     — бизнес-логика и обёртки над API (Tasks, Roles, Users, TaskProblemTypes, TaskPriorities)
  layouts/      — лейауты страниц (AdminLayout, AppLayout, AuthLayout, TasksLayout, SettingsLayout)
  lib/          — утилиты (cn, TokenManager, bbCodeConverter)
  pages/        — Inertia-страницы. Резолвятся в app.ts через import.meta.glob
  routes/       — генерируется Wayfinder, gitignored, НЕ ТРОГАТЬ
  services/
    api/          — per-resource axios-обёртки (типизированные)
    lib/          — axios instance с error-интерсептором
    service/      — ApiService с auth-хедером
  store/        — Pinia stores (modalStore, breadcrumbStore)
  types/        — TS-типы, разбиты по сущностям (entities/tasks, entities/users, entities/roles, entities/api)
    shims/        — module-shim'ы для пакетов без типов (vs-vue3-select)
  wayfinder/    — генерируется Wayfinder, gitignored, НЕ ТРОГАТЬ
  app.ts        — клиентский entry
  ssr.ts        — SSR entry
```

Принцип:
- `@/components`, `@/features`, `@/composables`, `@/types`, `@/store`, `@/services` — barrel-импорты через `index.ts`. При добавлении нового — дописывать в локальный `index.ts`.

## Конвенции

### Vue SFC
- Только `<script setup lang="ts">`. Без Options API.
- Порядок: `<template>` → `<script setup>` → `<style scoped>`. Стили почти всегда отсутствуют (Tailwind utility classes).
- Имена компонентов — **PascalCase** (`TaskList.vue`, `PerPageSelector.vue`).
- Composables — **camelCase** с префиксом `use` (`useToast`, `usePerPage`).

### Props / Emits
- Через TS-интерфейсы:
  ```ts
  interface Props {
      modelValue?: SelectOption | null
      placeholder?: string
  }
  interface Emits {
      (e: 'update:modelValue', value: SelectOption | null): void
  }
  const props = withDefaults(defineProps<Props>(), { modelValue: null, placeholder: '...' })
  const emit = defineEmits<Emits>()
  ```
- `defineModel` НЕ используется — везде классический `v-model` через `modelValue` + `update:modelValue`.

### Реактивность
- `ref<T>(initial)` — всегда с явным дженериком. **Не оставлять `ref()` или `ref([])`** без типа: получится `any` / `never[]`.
- `computed<T>(() => ...)` — указывать тип, когда он неочевиден.

### Импорты типов
- Через `import type { Foo } from "@/types"` или `import { type Foo } from "@/types"`.
- `SelectOption` — **единый источник в `@/types`**. Дженерик: `SelectOption<T extends number | string = number | string>`.
- Не объявлять `SelectOption` локально внутри компонентов.

### Язык
- Комментарии в коде и UI-тексты — **на русском**.

### TypeScript
- `strict: true`. Path alias: `@/* → resources/js/*`. `moduleResolution: bundler`.
- **Избегать `any`**. ESLint правило `@typescript-eslint/no-explicit-any` выключено — это историческое послабление, но в новом коде целимся в отсутствие `any`. Когда тип реально «любой» — `unknown` с явным narrowing.
- Дженерики и точные типы предпочтительнее union-«широких» типов. Пример: `SelectOption<number>` вместо `SelectOption` с `value: number | string`.

## Styling (Tailwind 4)

### Где живут токены
- `resources/css/app.css` — единственное место. Внутри:
  - `@theme inline { ... }` — токены (color-*, radius-*, font-sans).
  - `:root { --background: ...; ... }` — HSL CSS-переменные для светлой темы.
  - `.dark { ... }` — переопределение для тёмной темы.
  - `@custom-variant dark (&:is(.dark *));` — переменная `dark:` для Tailwind.
- `tailwind.config.js` **отсутствует** (Tailwind 4 не требует).

### Доступные токены
- **Цвета** (используй как `bg-background`, `text-foreground`, `border-border`, `bg-primary text-primary-foreground` и т.п.):
  `background`, `foreground`, `card[/-foreground]`, `popover[/-foreground]`, `primary[/-foreground]`, `secondary[/-foreground]`, `muted[/-foreground]`, `accent[/-foreground]`, `destructive[/-foreground]`, `border`, `input`, `ring`, `chart-1..5`, `sidebar`, `sidebar-foreground`, `sidebar-primary[/-foreground]`, `sidebar-accent[/-foreground]`, `sidebar-border`, `sidebar-ring`.
- **Радиус**: `rounded-sm/-md/-lg` (через `--radius-*`, базовый `--radius: 0.5rem`).
- **Шрифт**: `font-sans` (Instrument Sans).

### Правила
- **Не использовать произвольные значения цветов и spacing** вне токенов. `bg-[#abcdef]` или `mt-[13px]` — ❌. Если нужного токена нет — добавить в `@theme` и обсудить.
- `@apply` оправдан только в `@layer base` для глобальных ресетов (см. `body`, `*` в текущем `app.css`). В компонентах — utility classes напрямую.
- `cn(...)` из `@/lib/utils` — для условного склеивания классов (использует `clsx` + `tailwind-merge`).
- Prettier плагин `prettier-plugin-tailwindcss` сам сортирует классы — не сортируй вручную. Распознаваемые функции: `clsx`, `cn`, `cva`.

## State management

- **Pinia 3**, setup-стиль:
  ```ts
  export const useModalStore = defineStore('modal', () => {
      const isOpen = ref(false)
      const openModal = (component, options) => { ... }
      return { isOpen, openModal }
  })
  ```
- Сторы лежат в `resources/js/store/components/`.
- Тостами **не управляет Pinia** — `useToast` хранит состояние в модульном `ref` внутри composable (`composables/components/useToast.ts`). Историческое отклонение от паттерна.

## API layer

- **`services/lib/api.instance.ts`** — axios instance + response-интерсептор. Интерсептор:
  - Парсит `error: true` / `message` в ответе с 2xx и бросает `APIError`.
  - Парсит axios-ошибки и бросает `APIError` с полями `message`, `errors`, `status`.
  - **Показывает тост на любую ошибку** — поэтому в `catch` компонента **не** показывать ещё один тост, только откатывать состояние.
- **`services/service/api.service.ts`** (`ApiService`) — обёртка `get/post/patch/delete`, добавляет `Authorization` хедер из `TokenManager`.
- **`services/api/*`** — per-resource типизированные обёртки, возвращают `Promise<AxiosResponse<T>>`.
- **`features/*`** — бизнес-логика: вызывают сервисы, разворачивают `response.data`, делают доп. обработки (например, кэш в localStorage для справочников).
- **Типы DTO** живут в `resources/js/types/entities/*/index.d.ts`.
- При добавлении нового API-ресурса: создать `services/api/<resource>/index.ts` + `features/<resource>/index.ts` + типы в `types/entities/<resource>/`. Зарегистрировать сервис в `services/index.ts` (объект `API`). См. скил `api-feature-author`.

## Testing

- **Тесты фронтенда в проекте не настроены.** Vitest нет.
- Pest (PHP) — для бэкенда; см. корневой `CLAUDE.md`.

## Подмеченные ловушки

- **vs-vue3-select** не отдаёт типы через `package.json#exports`. Shim — `resources/js/types/shims/vs-vue3-select.d.ts`. Если меняешь модуль / удаляешь shim — type-check сразу ляжет.
- **`TaskQueryParams`** в `types/entities/tasks/index.d.ts` исторически расходился с тем, что читает бэк (Laravel `request->query(...)`). Имена полей: `problem_type_id`, `status_id`, `priority_id`, `person_phone`, `date_start`, `date_end`. Не возвращай к старым именам.
- **`TasksPaginateCollection`** / **`UsersPaginateCollection`** на бэке возвращают `data` + `has_more` + `current_page`. Фронт использует `has_more` для кнопки «Загрузить ещё». Новые ресурсы с пагинацией — тот же контракт.
- **`PROBLEM_TYPES_NAME` в `TaskCreateForm`** — захардкоженные ID типов проблем (1..5). Должны совпадать с бэкендовым `App\Http\Modules\Tasks\Enums\TaskProblemTypesEnum`.
- **Импорт `SelectOption`** — из `@/types`, не из `@/components` (старый путь работает, но новый код через `@/types`).
- **`DateRangePicker` + `ref`**: `DateValue` из `@internationalized/date` имеет nominal `#private`, который Vue `UnwrapRef` теряет. Поэтому при биндинге `ref`-овой `dateRange` нужно `:model-value="(dateRange as DateRange | undefined)"` (см. `TaskListFilter.vue`).
