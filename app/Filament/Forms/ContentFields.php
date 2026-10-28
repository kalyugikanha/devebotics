<?php

namespace App\Filament\Forms;

use App\Support\IconRegistry;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

/**
 * Shared form fields so every content resource behaves consistently.
 */
final class ContentFields
{
    /**
     * Title input that fills the slug while creating.
     */
    public static function title(string $field = 'title', string $label = 'Title'): TextInput
    {
        return TextInput::make($field)
            ->label($label)
            ->required()
            ->maxLength(180)
            ->live(onBlur: true)
            ->afterStateUpdated(function (Get $get, Set $set, ?string $state, string $operation): void {
                if ($operation === 'create' && blank($get('slug'))) {
                    $set('slug', Str::slug((string) $state));
                }
            });
    }

    public static function slug(string $table, string $prefix = '/'): TextInput
    {
        return TextInput::make('slug')
            ->label('URL slug')
            ->prefix(rtrim(url($prefix), '/').'/')
            ->maxLength(180)
            ->rule('alpha_dash')
            ->unique($table, 'slug', ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule)
            ->helperText('Lowercase letters, numbers and dashes. Changing the slug of published content creates a 301 redirect automatically.');
    }

    public static function richEditor(string $field, string $label, bool $required = false): RichEditor
    {
        return RichEditor::make($field)
            ->label($label)
            ->required($required)
            ->toolbarButtons([
                ['bold', 'italic', 'underline', 'strike', 'link', 'code'],
                ['h2', 'h3', 'h4'],
                ['blockquote', 'codeBlock', 'bulletList', 'orderedList', 'horizontalRule'],
                ['table', 'attachFiles'],
                ['undo', 'redo'],
            ])
            ->fileAttachmentsDisk(config('softec.media.disk', 'public'))
            ->fileAttachmentsDirectory('editor')
            ->fileAttachmentsVisibility('public')
            ->helperText('Formatting is sanitised on save: scripts, inline styles and embeds are removed.')
            ->columnSpanFull();
    }

    public static function icon(string $field = 'icon'): Select
    {
        return Select::make($field)
            ->label('Icon')
            ->options(collect(IconRegistry::icons())->mapWithKeys(fn (string $label, string $name) => [
                $name => '<span class="flex items-center gap-2">'.svg('heroicon-o-'.$name, 'h-5 w-5')->toHtml().'<span>'.e($label).'</span></span>',
            ])->all())
            ->allowHtml()
            ->searchable()
            ->in(array_keys(IconRegistry::icons()));
    }

    /**
     * Ordered list of {title, description, icon} items stored as JSON.
     */
    public static function listItems(string $field, string $label, int $max = 12, bool $withIcon = true): Repeater
    {
        return Repeater::make($field)
            ->label($label)
            ->schema(array_values(array_filter([
                TextInput::make('title')->required()->maxLength(120),
                Textarea::make('description')->rows(2)->maxLength(500),
                $withIcon ? self::icon() : null,
            ])))
            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
            ->collapsible()
            ->collapsed()
            ->reorderableWithButtons()
            ->defaultItems(0)
            ->maxItems($max)
            ->addActionLabel('Add item');
    }

    /**
     * Status, publication date, featured flag and demo flag.
     *
     * @param  class-string<BackedEnum>  $statusEnum
     */
    public static function publishing(string $statusEnum, string $managePermission, bool $featured = true): Section
    {
        return Section::make('Publishing')
            ->icon('heroicon-o-globe-alt')
            ->disabled(fn (): bool => ! Auth::user()?->can($managePermission))
            ->schema(array_values(array_filter([
                ToggleButtons::make('status')
                    ->options($statusEnum)
                    ->default('draft')
                    ->inline()
                    ->required(),
                DateTimePicker::make('published_at')
                    ->label('Publish date')
                    ->seconds(false)
                    ->native(false)
                    ->helperText('Leave empty to publish immediately. A future date schedules publication.'),
                $featured ? Toggle::make('is_featured')->label('Featured') : null,
                Toggle::make('is_demo')
                    ->label('Demo content')
                    ->helperText('Seeded demonstration content is labelled publicly. Untick once replaced with real, verified content.')
                    ->visible(fn (Get $get): bool => (bool) $get('is_demo')),
            ])));
    }

    /**
     * Closure that disables content fields for users without the permission
     * (e.g. SEO managers who may only edit the SEO section). Disabled fields
     * are not dehydrated, so tampered values are never saved.
     */
    public static function lockedUnless(string $permission): \Closure
    {
        return fn (): bool => ! Auth::user()?->can($permission);
    }
}
