<?php

namespace App\Filament\Resources\SecurityLogs;

use App\Filament\Resources\AuditLogs\AuditLogResource;
use App\Filament\Resources\SecurityLogs\Pages\ListSecurityLogs;
use App\Filament\Resources\SecurityLogs\Pages\ViewSecurityLog;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

/**
 * Sign-ins, failed attempts, lockouts, password resets, role and account changes.
 */
class SecurityLogResource extends AuditLogResource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFingerPrint;

    protected static ?int $navigationSort = 40;

    protected static ?string $navigationLabel = 'Login & security activity';

    protected static ?string $modelLabel = 'security event';

    protected static ?string $slug = 'security-activity';

    public static function securityEventsOnly(): bool
    {
        return true;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSecurityLogs::route('/'),
            'view' => ViewSecurityLog::route('/{record}'),
        ];
    }
}
