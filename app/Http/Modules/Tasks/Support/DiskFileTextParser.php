<?php

namespace App\Http\Modules\Tasks\Support;

/**
 * Разбор маркеров вложений Битрикс-Диска вида [DISK FILE ID=nXXX] в тексте задачи
 * (описание, результаты). Букву n в ID отбрасываем — значим только номер.
 */
class DiskFileTextParser
{
    private const MARKER = '/\[DISK FILE ID=n?(\d+)\]/i';

    /**
     * Все ID вложений, встречающиеся в тексте.
     *
     * @return int[]
     */
    public static function extractIds(?string $text): array
    {
        if (empty($text)) {
            return [];
        }

        preg_match_all(self::MARKER, $text, $matches);

        return array_map('intval', $matches[1]);
    }

    /**
     * Маркеры вложений как есть (с префиксом n или без — в Битриксе это разные ID),
     * чтобы перенести их в отредактированный текст.
     *
     * @return string[]
     */
    public static function extractMarkers(?string $text): array
    {
        if (empty($text)) {
            return [];
        }

        preg_match_all(self::MARKER, $text, $matches);

        return array_values(array_unique($matches[0]));
    }

    /**
     * Текст без маркеров вложений — для рендера описания через редактор.
     */
    public static function strip(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        return trim((string) preg_replace(self::MARKER, '', $text));
    }
}
