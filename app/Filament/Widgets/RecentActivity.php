<?php

namespace App\Filament\Widgets;

use App\Models\AuditLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class RecentActivity extends TableWidget
{
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = ['lg' => 1, 'default' => 'full'];

    public static function canView(): bool
    {
        return (bool) Auth::user()?->can('audit_logs.view');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent activity')
            ->query(AuditLog::query()->where('is_security_event', false)->with('user')->latest('created_at'))
            ->paginated([6])
            ->defaultPaginationPageOption(6)
            ->columns([
                TextColumn::make('description')->limit(60)->description(fn (AuditLog $record): string => ($record->user?->name ?? 'System').' · '.$record->created_at?->diffForHumans()),
            ])
            ->recordUrl(fn (AuditLog $record): ?string => Route::has('filament.admin.resources.audit-logs.view')
                ? route('filament.admin.resources.audit-logs.view', $record)
                : null)
            ->emptyStateHeading('No activity yet');
    }
}
