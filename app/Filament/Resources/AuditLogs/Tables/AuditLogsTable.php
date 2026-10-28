<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use App\Models\AuditLog;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class AuditLogsTable
{
    public static function configure(Table $table, bool $securityOnly): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('When')->dateTime('M j, Y H:i')->sortable()->description(fn (AuditLog $record): string => $record->created_at?->diffForHumans() ?? ''),
                TextColumn::make('event')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, 'failed'), str_contains($state, 'lockout'), str_contains($state, 'deleted'), str_contains($state, 'deactivated') => 'danger',
                        str_contains($state, 'login'), str_contains($state, 'created'), str_contains($state, 'activated') => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('user.name')->label('User')->placeholder('System')->searchable(),
                TextColumn::make('description')->limit(80)->wrap()->searchable(),
                TextColumn::make('auditable_type')->label('Subject')->formatStateUsing(fn (?string $state): string => $state ? Str::headline($state) : '—')->toggleable(),
                TextColumn::make('ip_address')->label('IP')->toggleable(isToggledHiddenByDefault: ! $securityOnly),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->options(fn (): array => AuditLog::query()->where('is_security_event', $securityOnly)->distinct()->orderBy('event')->pluck('event', 'event')->all())
                    ->multiple(),
                SelectFilter::make('user_id')->label('User')->relationship('user', 'name')->searchable()->preload(),
                SelectFilter::make('auditable_type')
                    ->label('Subject type')
                    ->options(fn (): array => AuditLog::query()->where('is_security_event', $securityOnly)->whereNotNull('auditable_type')->distinct()->orderBy('auditable_type')->pluck('auditable_type', 'auditable_type')->map(fn (string $type) => Str::headline($type))->all()),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('until')->label('Until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date))),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->emptyStateHeading($securityOnly ? 'No security events recorded' : 'No audit entries yet');
    }
}
