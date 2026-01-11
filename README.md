# Личный кабинет клиентов FlashFormat

### Установка:
- cp .env.example .env
- php artisan key:generate
- php artisan jwt:secret

Заменить в _**.env**_ **B24_WEBHOOK** на актуальный входящий вебхук из Битрикс24 с необходимыми правами.

### Битрикс24

#### Права доступа у входящего вебхука:
- CRM (crm)
- Пользователи (user)
- Рабочие группы (sonet_group)
- Социальная сеть (socialnetwork)
- Задачи (tasks)
- Задачи (task)
- Информационные блоки (iblock)
- Диск (disk)
