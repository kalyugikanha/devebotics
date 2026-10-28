<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\TextInput;

/**
 * URL input that accepts site-relative paths ("/contact") or http(s) URLs only,
 * so dangerous schemes such as javascript: or data: can never be stored.
 */
final class LinkInput
{
    public const PATTERN = '#^(/(?!/)[^\s<>"]*|https?://[^\s<>"]+)$#i';

    public static function make(string $field, string $label = 'Link', bool $httpsOnly = false): TextInput
    {
        return TextInput::make($field)
            ->label($label)
            ->maxLength(2048)
            ->regex($httpsOnly ? '#^(/(?!/)[^\s<>"]*|https://[^\s<>"]+)$#i' : self::PATTERN)
            ->validationMessages(['regex' => $httpsOnly
                ? 'Use a site path starting with "/" or a full https:// URL.'
                : 'Use a site path starting with "/" or a full http(s):// URL.'])
            ->placeholder('/contact or https://example.com');
    }
}
