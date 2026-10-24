<?php

use App\Http\Modules\Notifications\Services\TelegramNotifyService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    // ID ответственного в Битриксе зависит от окружения — задаём свой, а не берём из .env
    $this->responsibleId = fake()->numberBetween(1, 99999);
    config([
        'b24.domain' => 'https://bitrix.test',
        'b24.default.responsibleId' => $this->responsibleId,
        'telegram.notify.hook_url' => 'https://tele.test/hook',
        'telegram.notify.hook_key' => 'test-key',
    ]);
});

it('отправляет уведомление в Telegram-хук с ключом и ссылкой на задачу Битрикса', function () {
    Http::fake();
    $bTaskId = fake()->numberBetween(1, 99999);

    (new TelegramNotifyService)->notifyTaskCreated('Не работает 1С', $bTaskId);

    $expectedUrl = "https://bitrix.test/company/personal/user/{$this->responsibleId}/tasks/task/view/{$bTaskId}/";
    Http::assertSent(function ($request) use ($expectedUrl) {
        return str_starts_with($request->url(), 'https://tele.test/hook')
            && $request['key'] === 'test-key'
            && str_contains($request['text'], 'Создано новое обращение в личном кабинете')
            && str_contains($request['text'], 'Название: Не работает 1С')
            && str_contains($request['text'], 'URL: '.$expectedUrl);
    });
});
