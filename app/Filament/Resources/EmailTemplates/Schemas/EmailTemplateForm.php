<?php

namespace App\Filament\Resources\EmailTemplates\Schemas;

use App\Models\EmailTemplate;
use App\Support\Mail\EmailTemplateRegistry;
use Closure;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class EmailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()->columnSpan(['lg' => 2])->schema([
                    Section::make('Content')->schema([
                        TextInput::make('subject')->required()->maxLength(200)->rule(self::placeholderRule()),
                        TextInput::make('heading')->maxLength(200)->rule(self::placeholderRule()),
                        Textarea::make('body')
                            ->required()
                            ->rows(14)
                            ->maxLength(10000)
                            ->rule(self::placeholderRule())
                            ->helperText('Markdown supported: **bold**, *italic*, lists and links. Raw HTML is removed. Placeholder values are always escaped.'),
                        TextInput::make('action_label')->label('Button label')->maxLength(60)->helperText('Shown only when the email includes a link.'),
                    ]),
                ]),
                Group::make()->columnSpan(['lg' => 1])->schema([
                    Section::make('Template')->schema([
                        TextEntry::make('name'),
                        TextEntry::make('key')->badge()->color('gray'),
                        Toggle::make('is_active')->label('Active')->helperText('Inactive templates are not sent.'),
                    ]),
                    Callout::make('Available placeholders')
                        ->description(fn (?EmailTemplate $record): HtmlString => new HtmlString(
                            collect(self::allowedVariables($record?->key))
                                ->map(fn (string $variable) => '<code>{{ '.e($variable).' }}</code>')
                                ->implode(' ')
                        ))
                        ->info(),
                ]),
            ]);
    }

    /**
     * @return array<int, string>
     */
    public static function allowedVariables(?string $key): array
    {
        return array_values(array_unique(array_merge(EmailTemplateRegistry::variables((string) $key), ['company_name'])));
    }

    /**
     * Rejects placeholders that are not available for this template.
     */
    private static function placeholderRule(): Closure
    {
        return fn (?EmailTemplate $record): Closure => function (string $attribute, $value, Closure $fail) use ($record): void {
            preg_match_all('/\{\{\s*([A-Za-z_]+)\s*\}\}/', (string) $value, $matches);
            $unknown = array_diff(array_unique($matches[1]), self::allowedVariables($record?->key));

            if ($unknown !== []) {
                $fail('Unknown placeholder(s): '.implode(', ', $unknown).'.');
            }
        };
    }
}
