<?php

use App\Http\Modules\Notifications\Services\TelegramNotifyService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'b24.domain' => 'https://bitrix.test',
        'telegram.notify.hook_url' => 'https://tele.test/hook',
        'telegram.notify.hook_key' => 'test-key',
    ]);
});

it('отправляет уведомление в Telegram-хук с ключом и ссылкой на задачу Битрикса', function () {
    Http::fake();

    (new TelegramNotifyService)->notifyTaskCreated('Не работает 1С', 4321);

    Http::assertSent(function ($request) {
        return str_starts_with($request->url(), 'https://tele.test/hook')
            && $request['key'] === 'test-key'
            && str_contains($request['text'], 'Создано новое обращение в личном кабинете')
            && str_contains($request['text'], 'Название: Не работает 1С')
            && str_contains($request['text'], 'URL: https://bitrix.test/company/personal/user/312/tasks/task/view/4321/');
    });
});
