<?php

namespace App\Filament\Pages;

use App\Filament\AdminNavigationGroup;
use App\Models\FailedJob;
use App\Support\Audit\AuditLogger;
use App\Support\Settings\Settings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as DatabaseSchema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Spatie\Backup\BackupDestination\BackupDestination;
use Throwable;
use UnitEnum;

/**
 * Operational overview for super administrators: environment, queue,
 * scheduler, cache, backups and maintenance mode. Every action is audited.
 */
class SystemHealth extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::System;

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'System health';

    protected static ?string $title = 'System health';

    protected static ?string $slug = 'system-health';

    public static function canAccess(): bool
    {
        return (bool) Auth::user()?->can('system.manage');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Callout::make('Debug mode is enabled in production')
                ->description('Set APP_DEBUG=false in the production environment immediately — debug pages can expose secrets.')
                ->danger()
                ->visible(fn (): bool => App::isProduction() && (bool) config('app.debug')),
            Callout::make('The site is in maintenance mode')
                ->description('Visitors currently see the maintenance page. Use “Bring site online” when you are done.')
                ->warning()
                ->visible(fn (): bool => App::isDownForMaintenance()),
            Grid::make(['lg' => 2])->schema([
                Section::make('Application')->icon('heroicon-o-cpu-chip')->columns(2)->schema([
                    TextEntry::make('app_env')->label('Environment')->state(fn (): string => App::environment())->badge()
                        ->color(fn (): string => App::isProduction() ? 'success' : 'warning'),
                    IconEntry::make('app_debug')->label('Debug mode off')->state(fn (): bool => ! config('app.debug'))->boolean(),
                    TextEntry::make('php')->label('PHP')->state(PHP_VERSION),
                    TextEntry::make('laravel')->label('Laravel')->state(fn (): string => App::version()),
                    IconEntry::make('https')->label('HTTPS enforced')->state(fn (): bool => (bool) config('softec.force_https'))->boolean(),
                    IconEntry::make('storage_link')->label('Public storage linked')->state(fn (): bool => is_link(public_path('storage')) || is_dir(public_path('storage')))->boolean(),
                    IconEntry::make('config_cached')->label('Configuration cached')->state(fn (): bool => App::configurationIsCached())->boolean(),
                    IconEntry::make('routes_cached')->label('Routes cached')->state(fn (): bool => App::routesAreCached())->boolean(),
                ]),
                Section::make('Queue, scheduler & services')->icon('heroicon-o-queue-list')->columns(2)->schema([
                    TextEntry::make('queue')->label('Queue connection')->state(fn (): string => (string) config('queue.default')),
                    TextEntry::make('pending_jobs')->label('Pending jobs')->state(fn (): string => $this->pendingJobs()),
                    TextEntry::make('failed_jobs')->label('Failed jobs')->state(fn (): int => DatabaseSchema::hasTable('failed_jobs') ? FailedJob::query()->count() : 0)->badge()
                        ->color(fn ($state): string => $state > 0 ? 'danger' : 'success'),
                    TextEntry::make('scheduler')->label('Scheduler heartbeat')->state(fn (): string => $this->schedulerStatus())
                        ->color(fn (): string => $this->schedulerHealthy() ? 'success' : 'danger')->badge(),
                    TextEntry::make('cache_store')->label('Cache store')->state(fn (): string => (string) config('cache.default')),
                    TextEntry::make('session_driver')->label('Session driver')->state(fn (): string => (string) config('session.driver')),
                    TextEntry::make('mail')->label('Mail transport')->state(fn (): string => (string) config('mail.default')),
                    TextEntry::make('media_disk')->label('Media disk')->state(fn (): string => (string) config('softec.media.disk')),
                ]),
            ]),
            Section::make('Backups')->icon('heroicon-o-archive-box')->schema([
                TextEntry::make('backups')->hiddenLabel()->state(fn (): HtmlString => $this->backupsSummary()),
            ]),
            Section::make('Failed queue jobs')->icon('heroicon-o-exclamation-triangle')->schema([
                EmbeddedTable::make(),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(FailedJob::query())
            ->defaultSort('failed_at', 'desc')
            ->columns([
                TextColumn::make('failed_at')->label('Failed')->since()->sortable(),
                TextColumn::make('job')->label('Job')->state(fn (FailedJob $record): string => $record->displayName()),
                TextColumn::make('queue'),
                TextColumn::make('exception')->label('Error')->state(fn (FailedJob $record): string => Str::limit($record->exceptionSummary(), 140))->wrap(),
            ])
            ->recordActions([
                Action::make('retry')
                    ->icon('heroicon-o-arrow-path')
                    ->authorize(fn (): bool => static::canAccess())
                    ->action(function (FailedJob $record): void {
                        Artisan::call('queue:retry', ['id' => [$record->uuid]]);
                        $this->audit('system.failed_job_retried', 'Retried failed job '.$record->displayName());
                        Notification::make()->title('Job pushed back onto the queue')->success()->send();
                    }),
                Action::make('forget')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->authorize(fn (): bool => static::canAccess())
                    ->action(function (FailedJob $record): void {
                        Artisan::call('queue:forget', ['id' => $record->uuid]);
                        $this->audit('system.failed_job_deleted', 'Deleted failed job '.$record->displayName());
                        Notification::make()->title('Failed job deleted')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkAction::make('retrySelected')
                    ->label('Retry selected')
                    ->icon('heroicon-o-arrow-path')
                    ->authorize(fn (): bool => static::canAccess())
                    ->action(function (Collection $records): void {
                        Artisan::call('queue:retry', ['id' => $records->pluck('uuid')->all()]);
                        $this->audit('system.failed_jobs_retried', 'Retried '.$records->count().' failed job(s)');
                        Notification::make()->title('Jobs pushed back onto the queue')->success()->send();
                    }),
            ])
            ->emptyStateHeading('No failed jobs')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated([10, 25]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearCache')
                ->label('Clear application cache')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Clears the application cache (settings, navigation, sitemaps, redirects) and compiled views. Cached configuration and routes are kept in production — re-run `php artisan optimize` during deployment instead.')
                ->authorize(fn (): bool => static::canAccess())
                ->action(function (): void {
                    Artisan::call('cache:clear');
                    Artisan::call('view:clear');

                    if (! App::isProduction() && App::configurationIsCached()) {
                        Artisan::call('config:clear');
                    }

                    app(Settings::class)->flush();
                    $this->audit('system.cache_cleared', 'Application cache cleared');
                    Notification::make()->title('Cache cleared')->success()->send();
                }),
            Action::make('runBackup')
                ->label('Back up database now')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Queues a database backup to the configured backup disk. Requires a running queue worker and the mysqldump client on the server.')
                ->authorize(fn (): bool => static::canAccess())
                ->action(function (): void {
                    Artisan::queue('backup:run', ['--only-db' => true, '--disable-notifications' => true]);
                    $this->audit('system.backup_queued', 'Database backup queued');
                    Notification::make()->title('Backup queued')->body('Refresh this page in a few minutes to see it listed.')->success()->send();
                }),
            Action::make('maintenanceOn')
                ->label('Enable maintenance mode')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('warning')
                ->visible(fn (): bool => ! App::isDownForMaintenance())
                ->authorize(fn (): bool => static::canAccess())
                ->modalDescription('Visitors will see the maintenance page. Copy the bypass link below BEFORE confirming — it is the only way to keep using the site and admin panel while maintenance mode is on.')
                ->fillForm(fn (): array => ['secret' => Str::lower(Str::random(32))])
                ->schema([
                    TextInput::make('secret')
                        ->label('Bypass link')
                        ->readOnly()
                        ->prefix(url('/').'/')
                        ->copyable()
                        ->helperText('Visiting this link sets a cookie that lets you through the maintenance page.'),
                ])
                ->action(function (array $data): void {
                    $secret = (string) $data['secret'];
                    Artisan::call('down', array_filter([
                        '--secret' => $secret,
                        '--render' => view()->exists('errors.503') ? 'errors::503' : null,
                        '--retry' => 60,
                    ]));
                    $this->audit('system.maintenance_enabled', 'Maintenance mode enabled');
                    $this->redirect(url('/'.$secret));
                }),
            Action::make('maintenanceOff')
                ->label('Bring site online')
                ->icon('heroicon-o-play')
                ->color('success')
                ->visible(fn (): bool => App::isDownForMaintenance())
                ->requiresConfirmation()
                ->authorize(fn (): bool => static::canAccess())
                ->action(function (): void {
                    Artisan::call('up');
                    $this->audit('system.maintenance_disabled', 'Maintenance mode disabled');
                    Notification::make()->title('The site is online')->success()->send();
                }),
        ];
    }

    public function schedulerHealthy(): bool
    {
        $beat = Cache::get('scheduler.heartbeat');

        return $beat !== null && Carbon::parse($beat)->greaterThan(now()->subMinutes(5));
    }

    protected function schedulerStatus(): string
    {
        $beat = Cache::get('scheduler.heartbeat');

        if ($beat === null) {
            return 'No heartbeat — is the cron entry for schedule:run configured?';
        }

        return ($this->schedulerHealthy() ? 'Running' : 'Stalled').' · last run '.Carbon::parse($beat)->diffForHumans();
    }

    protected function pendingJobs(): string
    {
        if (config('queue.default') === 'sync') {
            return 'n/a (sync driver)';
        }

        if (config('queue.default') === 'database' && DatabaseSchema::hasTable('jobs')) {
            return (string) DB::table('jobs')->count();
        }

        return 'See your queue dashboard';
    }

    protected function backupsSummary(): HtmlString
    {
        $name = config('backup.backup.name');
        $disks = (array) config('backup.backup.destination.disks', []);

        if (blank($name) || $disks === []) {
            return new HtmlString('Backups are not configured. See the deployment guide for spatie/laravel-backup setup.');
        }

        $rows = [];

        foreach ($disks as $disk) {
            try {
                $destination = BackupDestination::create($disk, (string) $name);
                $backups = $destination->backups()->take(5);

                if ($backups->isEmpty()) {
                    $rows[] = '<li><strong>'.e($disk).'</strong>: no backups yet</li>';

                    continue;
                }

                foreach ($backups as $backup) {
                    $rows[] = '<li><strong>'.e($disk).'</strong>: '.e($backup->date()->toDayDateTimeString()).' · '.e(Number::fileSize($backup->sizeInBytes(), 1)).'</li>';
                }
            } catch (Throwable $exception) {
                $rows[] = '<li><strong>'.e($disk).'</strong>: unreachable ('.e(class_basename($exception)).')</li>';
            }
        }

        return new HtmlString('<ul class="list-disc space-y-1 ps-5">'.implode('', $rows).'</ul><p class="mt-3 text-sm text-gray-500">Daily database backups run from the scheduler when it is configured. Test restores regularly — see the deployment guide.</p>');
    }

    private function audit(string $event, string $description): void
    {
        app(AuditLogger::class)->log($event, null, description: $description, isSecurityEvent: true);
    }
}
