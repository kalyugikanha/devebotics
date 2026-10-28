<?php

namespace App\Filament\Resources\Redirects;

use App\Enums\RedirectType;
use App\Filament\AdminNavigationGroup;
use App\Filament\Resources\Redirects\Pages\CreateRedirect;
use App\Filament\Resources\Redirects\Pages\EditRedirect;
use App\Filament\Resources\Redirects\Pages\ListRedirects;
use App\Models\Redirect;
use BackedEnum;
use Closure;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnRight;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Seo;

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'from_path';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Redirect')->columns(2)->schema([
                TextInput::make('from_path')
                    ->label('From path')
                    ->required()
                    ->maxLength(512)
                    ->placeholder('/old-page')
                    ->regex('/^\/[A-Za-z0-9\-._~\/%]*$/')
                    ->validationMessages(['regex' => 'Enter a site path starting with “/” (no domain or query string).'])
                    ->unique('redirects', 'from_path', ignoreRecord: true)
                    ->dehydrateStateUsing(fn (?string $state): string => Redirect::normalizePath((string) $state)),
                Select::make('status_code')
                    ->label('Type')
                    ->options(RedirectType::class)
                    ->default(RedirectType::Permanent->value)
                    ->required()
                    ->live(),
                TextInput::make('to_url')
                    ->label('Destination')
                    ->maxLength(2048)
                    ->placeholder('/new-page or https://example.com/page')
                    ->required(fn (Get $get): bool => (int) self::statusValue($get('status_code')) !== RedirectType::Gone->value)
                    ->hidden(fn (Get $get): bool => (int) self::statusValue($get('status_code')) === RedirectType::Gone->value)
                    ->regex('/^(\/[^\s]*|https:\/\/[^\s]+)$/')
                    ->validationMessages(['regex' => 'Use a site path starting with “/” or a full https:// URL.'])
                    ->rule(fn (Get $get, ?Redirect $record): Closure => self::loopRule((string) $get('from_path'), $record))
                    ->columnSpanFull(),
                Toggle::make('is_active')->label('Active')->default(true),
                TextInput::make('note')->maxLength(255),
            ]),
            Section::make('Usage')->hiddenOn('create')->columns(3)->schema([
                TextEntry::make('hits')->numeric(),
                TextEntry::make('last_hit_at')->label('Last used')->since()->placeholder('Never'),
                TextEntry::make('is_automatic')->label('Origin')->formatStateUsing(fn ($state): string => $state ? 'Created automatically after a slug change' : 'Created manually'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('from_path')->label('From')->searchable()->sortable()->fontFamily('mono')->copyable(),
                TextColumn::make('to_url')->label('To')->searchable()->fontFamily('mono')->placeholder('— (gone)')->limit(60),
                TextColumn::make('status_code')->label('Type')->badge()
                    ->color(fn ($state): string => match ((int) self::statusValue($state)) {
                        301 => 'success', 302 => 'info', default => 'danger',
                    })
                    ->formatStateUsing(fn ($state): string => (string) self::statusValue($state)),
                IconColumn::make('is_active')->label('Active')->boolean(),
                IconColumn::make('is_automatic')->label('Auto')->boolean()->trueIcon('heroicon-o-bolt')->falseIcon('heroicon-o-user')->trueColor('warning')->falseColor('gray'),
                TextColumn::make('hits')->numeric()->sortable(),
                TextColumn::make('last_hit_at')->label('Last used')->since()->sortable()->placeholder('Never'),
            ])
            ->filters([
                SelectFilter::make('status_code')->label('Type')->options(RedirectType::class),
                TernaryFilter::make('is_active')->label('Active'),
                TernaryFilter::make('is_automatic')->label('Automatic'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->emptyStateHeading('No redirects')
            ->emptyStateDescription('Redirects are created automatically when the slug of published content changes. You can also add them manually.')
            ->emptyStateActions([CreateAction::make()]);
    }

    /**
     * Rejects redirects to themselves and chains that loop back to the source.
     */
    public static function loopRule(string $fromPath, ?Redirect $record): Closure
    {
        return function (string $attribute, $value, Closure $fail) use ($fromPath, $record): void {
            if (blank($value) || str_starts_with((string) $value, 'http')) {
                return;
            }

            $from = Redirect::normalizePath($fromPath);
            $next = Redirect::normalizePath((string) $value);

            if ($next === $from) {
                $fail('A redirect cannot point to itself.');

                return;
            }

            $map = Redirect::query()
                ->where('is_active', true)
                ->when($record, fn ($q) => $q->whereKeyNot($record->getKey()))
                ->pluck('to_url', 'from_path')
                ->all();

            $seen = [$from => true];

            for ($hops = 0; $hops < 20 && isset($map[$next]); $hops++) {
                if (isset($seen[$next])) {
                    break;
                }

                $seen[$next] = true;
                $target = (string) $map[$next];

                if ($target === '' || str_starts_with($target, 'http')) {
                    return;
                }

                $next = Redirect::normalizePath($target);

                if ($next === $from) {
                    $fail('This would create a redirect loop.');

                    return;
                }
            }
        };
    }

    private static function statusValue(mixed $state): int|string|null
    {
        return $state instanceof RedirectType ? $state->value : $state;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRedirects::route('/'),
            'create' => CreateRedirect::route('/create'),
            'edit' => EditRedirect::route('/{record}/edit'),
        ];
    }
}
