<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use App\Models\AuditLog;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Event')
                    ->columnSpan(['lg' => 2])
                    ->columns(2)
                    ->schema([
                        TextEntry::make('event')->badge(),
                        TextEntry::make('created_at')->label('When')->dateTime('M j, Y H:i:s'),
                        TextEntry::make('user.name')->label('Performed by')->placeholder('System / anonymous'),
                        TextEntry::make('subject')
                            ->label('Subject')
                            ->state(fn (AuditLog $record): string => $record->auditable_type
                                ? Str::headline($record->auditable_type).' #'.$record->auditable_id
                                : '—'),
                        TextEntry::make('description')->columnSpanFull(),
                        IconEntry::make('is_security_event')->label('Security event')->boolean(),
                    ]),
                Section::make('Request')
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        TextEntry::make('ip_address')->label('IP address')->placeholder('—')->copyable(),
                        TextEntry::make('user_agent')->label('User agent')->placeholder('—'),
                        TextEntry::make('url')->label('URL')->placeholder('—'),
                    ]),
                Section::make('Changes')
                    ->columnSpanFull()
                    ->visible(fn (AuditLog $record): bool => filled($record->old_values) || filled($record->new_values))
                    ->schema([
                        ViewEntry::make('changes')->hiddenLabel()->view('filament.audit.diff'),
                    ]),
            ]);
    }
}
