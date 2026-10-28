<?php

namespace App\Filament\Resources\Announcements;

use App\Enums\AnnouncementStyle;
use App\Filament\AdminNavigationGroup;
use App\Filament\Forms\LinkInput;
use App\Filament\Resources\Announcements\Pages\ManageAnnouncements;
use App\Models\Announcement;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Website;

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'message';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Textarea::make('message')->required()->rows(2)->maxLength(300)->columnSpanFull(),
                TextInput::make('link_label')->maxLength(60)->placeholder('Learn more'),
                LinkInput::make('link_url', 'Link URL')->requiredWith('link_label'),
                ToggleButtons::make('style')->options(AnnouncementStyle::class)->default(AnnouncementStyle::Brand->value)->inline()->required()->columnSpanFull(),
                DateTimePicker::make('starts_at')->label('Show from')->seconds(false)->native(false),
                DateTimePicker::make('ends_at')->label('Show until')->seconds(false)->native(false)->after('starts_at'),
                Toggle::make('is_active')->label('Active')->default(true),
                Toggle::make('is_dismissible')->label('Visitors can dismiss it')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('message')->searchable()->wrap()->limit(90),
                TextColumn::make('style')->badge(),
                TextColumn::make('window')
                    ->label('Schedule')
                    ->state(fn (Announcement $record): string => ($record->starts_at?->format('M j, Y H:i') ?? 'Now').' → '.($record->ends_at?->format('M j, Y H:i') ?? 'No end'))
                    ->color('gray'),
                TextColumn::make('live')
                    ->label('Status')
                    ->badge()
                    ->state(fn (Announcement $record): string => $record->is_active && ($record->starts_at === null || $record->starts_at->isPast()) && ($record->ends_at === null || $record->ends_at->isFuture()) ? 'Live' : 'Not live')
                    ->color(fn (string $state): string => $state === 'Live' ? 'success' : 'gray'),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-megaphone')
            ->emptyStateHeading('No announcements')
            ->emptyStateDescription('The first live announcement is shown in the bar above the website header.')
            ->emptyStateActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAnnouncements::route('/'),
        ];
    }
}
