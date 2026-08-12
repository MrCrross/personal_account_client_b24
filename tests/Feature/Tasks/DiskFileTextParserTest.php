<?php

use App\Http\Modules\Tasks\Support\DiskFileTextParser;

it('извлекает ID вложений с префиксом n и без него', function () {
    $text = 'до [DISK FILE ID=n323669] и [DISK FILE ID=42] конец';

    expect(DiskFileTextParser::extractIds($text))->toBe([323669, 42]);
});

it('возвращает пустой массив ID для пустого текста', function () {
    expect(DiskFileTextParser::extractIds(null))->toBe([])
        ->and(DiskFileTextParser::extractIds(''))->toBe([]);
});

it('вырезает маркеры вложений из текста', function () {
    $text = 'Данные удалены [DISK FILE ID=n323669]';

    expect(DiskFileTextParser::strip($text))->toBe('Данные удалены');
});
