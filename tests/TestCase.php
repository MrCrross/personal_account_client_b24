<?php

namespace Tests;

use App\Http\Wrappers\BWebHookConstructor;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Тесты работают с Bitrix через вебхук + Http::fake(), независимо от B24_DRIVER в .env/окружении:
        // в sqlite :memory: таблиц b_* для драйвера database нет
        config(['b24.driver' => BWebHookConstructor::DRIVER_WEBHOOK]);
    }
}
