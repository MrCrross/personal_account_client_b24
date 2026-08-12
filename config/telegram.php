<?php

return [
    'notify' => [
        'hook_url' => env('TELEGRAM_NOTIFY_HOOK_URL', 'https://telegram.my.ru/hook'),
        'hook_key' => env('TELEGRAM_NOTIFY_HOOK_KEY'),
    ],
];
