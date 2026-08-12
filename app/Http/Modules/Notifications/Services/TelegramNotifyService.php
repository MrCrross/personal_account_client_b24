<?php

namespace App\Http\Modules\Notifications\Services;

use Illuminate\Support\Facades\Http;

class TelegramNotifyService
{
    public function notifyTaskCreated(string $taskName, int $bTaskId): void
    {
        $b24Domain = rtrim(config('b24.domain'), '/');
        $responsibleId = config('b24.default.responsibleId');
        $url = $b24Domain . '/company/personal/user/' . $responsibleId . '/tasks/task/view/' . $bTaskId . '/';

        $text = "Создано новое обращение в личном кабинете\n"
            . "Название: {$taskName}\n"
            . "URL: {$url}";

        Http::get(config('telegram.notify.hook_url'), [
            'key' => config('telegram.notify.hook_key'),
            'text' => $text,
        ]);
    }
}
