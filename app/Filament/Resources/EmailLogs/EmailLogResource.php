<?php

namespace App\Filament\Resources\EmailLogs;

use App\Enums\EmailStatus;
use App\Filament\AdminNavigationGroup;
use App\Filament\Resources\EmailLogs\Pages\ListEmailLogs;
use App\Models\EmailLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class EmailLogResource extends Resource
{
    protected static ?string $model = EmailLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Settings;

    protected static ?int $navigationSort = 50;

    protected static ?string $navigationLabel = 'Email log';

    protected static bool $isGloballySearchable = false;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Queued')->dateTime('M j, Y H:i')->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('template_key')->label('Template')->badge()->color('gray')->searchable(),
                TextColumn::make('recipient_masked')->label('Recipient'),
                TextColumn::make('subject')->limit(50)->searchable(),
                TextColumn::make('related_type')->label('Related to')
                    ->formatStateUsing(fn (?string $state, EmailLog $record): string => $state ? Str::headline($state).' #'.$record->related_id : '—')
                    ->toggleable(),
                TextColumn::make('sent_at')->label('Sent')->since()->placeholder('—')->sortable(),
                TextColumn::make('error')->limit(60)->wrap()->placeholder('—')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(EmailStatus::class),
                SelectFilter::make('template_key')->label('Template')
                    ->options(fn (): array => EmailLog::query()->whereNotNull('template_key')->distinct()->orderBy('template_key')->pluck('template_key', 'template_key')->all()),
            ])
            ->emptyStateHeading('No emails sent yet');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailLogs::route('/'),
        ];
    }
}
