<?php

namespace App\Support\Csv;

use Closure;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams CSV downloads with spreadsheet formula-injection protection:
 * cells beginning with = + - @ (or tab / carriage return) are prefixed with
 * a single quote so spreadsheet software treats them as text.
 */
final class CsvExport
{
    /**
     * @param  array<int, string>  $headers
     * @param  iterable<int, array<int, mixed>>|Closure(): iterable<int, array<int, mixed>>  $rows
     */
    public static function download(string $filename, array $headers, iterable|Closure $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $handle = fopen('php://output', 'wb');

            // UTF-8 BOM so spreadsheet apps detect the encoding.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, array_map([self::class, 'escapeCell'], $headers), escape: '');

            foreach ($rows instanceof Closure ? $rows() : $rows as $row) {
                fputcsv($handle, array_map([self::class, 'escapeCell'], $row), escape: '');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public static function escapeCell(mixed $value): string
    {
        if ($value instanceof \BackedEnum) {
            $value = $value->value;
        } elseif ($value instanceof \DateTimeInterface) {
            $value = $value->format('Y-m-d H:i:s');
        } elseif (is_bool($value)) {
            $value = $value ? 'yes' : 'no';
        } elseif (is_array($value)) {
            $value = json_encode($value);
        }

        $value = (string) $value;

        if ($value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$value;
        }

        return $value;
    }
}
