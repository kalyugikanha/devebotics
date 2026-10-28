<?php

namespace App\Filament\Resources\ContactInquiries\Actions;

use App\Enums\InquiryPriority;
use App\Enums\InquiryStatus;
use App\Models\ContactInquiry;
use App\Models\Team;
use App\Models\User;
use App\Services\Crm\InquiryManager;
use App\Support\Audit\AuditLogger;
use App\Support\Csv\CsvExport;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
 * CRM actions for inquiries (record pages, table rows and bulk selections).
 */
final class InquiryActions
{
    public static function assign(): Action
    {
        return Action::make('assign')
            ->label('Assign')
            ->icon('heroicon-o-user-plus')
            ->color('primary')
            ->authorize(fn (ContactInquiry $record): bool => Gate::allows('assign', $record))
            ->schema([self::assigneeSelect()])
            ->fillForm(fn (ContactInquiry $record): array => ['assigned_to' => $record->assigned_to])
            ->action(function (ContactInquiry $record, array $data): void {
                app(InquiryManager::class)->assign(Auth::user(), $record, self::resolveUser($data['assigned_to'] ?? null));
                Notification::make()->title('Assignment updated')->success()->send();
            });
    }

    /**
     * Pick up an unassigned inquiry from one of your teams' queues.
     */
    public static function claim(): Action
    {
        return Action::make('claim')
            ->label('Pick up')
            ->icon('heroicon-o-hand-raised')
            ->color('success')
            ->authorize(fn (ContactInquiry $record): bool => Gate::allows('claim', $record))
            ->requiresConfirmation()
            ->modalDescription('The inquiry will be assigned to you and leave the team queue.')
            ->action(function (ContactInquiry $record): void {
                app(InquiryManager::class)->claim(Auth::user(), $record);
                Notification::make()->title('Inquiry '.$record->reference.' is now yours')->success()->send();
            });
    }

    /**
     * Move an inquiry to another team (requires `inquiries.view_all`).
     */
    public static function moveToTeam(): Action
    {
        return Action::make('moveToTeam')
            ->label('Move to team')
            ->icon('heroicon-o-arrows-right-left')
            ->color('gray')
            ->authorize(fn (ContactInquiry $record): bool => Gate::allows('changeTeam', $record))
            ->schema([
                Select::make('team_id')
                    ->label('Team')
                    ->options(fn (): array => Team::query()->active()->ordered()->pluck('name', 'id')->all())
                    ->in(fn (): array => Team::query()->active()->pluck('id')->all())
                    ->placeholder('No team (unrouted)')
                    ->helperText('The current assignee is kept only if they belong to the new team.'),
            ])
            ->fillForm(fn (ContactInquiry $record): array => ['team_id' => $record->team_id])
            ->action(function (ContactInquiry $record, array $data): void {
                $team = filled($data['team_id'] ?? null) ? Team::query()->active()->find($data['team_id']) : null;
                app(InquiryManager::class)->moveToTeam(Auth::user(), $record, $team);
                Notification::make()->title('Team updated')->success()->send();
            });
    }

    public static function changeStatus(): Action
    {
        return Action::make('changeStatus')
            ->label('Change status')
            ->icon('heroicon-o-arrow-path')
            ->color('gray')
            ->authorize(fn (ContactInquiry $record): bool => Gate::allows('update', $record))
            ->schema([
                Select::make('status')->options(InquiryStatus::class)->required(),
                Textarea::make('note')->label('Note (optional)')->rows(2)->maxLength(2000),
            ])
            ->fillForm(fn (ContactInquiry $record): array => ['status' => $record->status])
            ->action(function (ContactInquiry $record, array $data): void {
                $status = $data['status'] instanceof InquiryStatus ? $data['status'] : InquiryStatus::from($data['status']);
                app(InquiryManager::class)->changeStatus(Auth::user(), $record, $status, $data['note'] ?? null);
                Notification::make()->title('Status updated')->success()->send();
            });
    }

    public static function changePriority(): Action
    {
        return Action::make('changePriority')
            ->label('Change priority')
            ->icon('heroicon-o-flag')
            ->color('gray')
            ->authorize(fn (ContactInquiry $record): bool => Gate::allows('update', $record))
            ->schema([Select::make('priority')->options(InquiryPriority::class)->required()])
            ->fillForm(fn (ContactInquiry $record): array => ['priority' => $record->priority])
            ->action(function (ContactInquiry $record, array $data): void {
                $priority = $data['priority'] instanceof InquiryPriority ? $data['priority'] : InquiryPriority::from($data['priority']);
                app(InquiryManager::class)->changePriority(Auth::user(), $record, $priority);
                Notification::make()->title('Priority updated')->success()->send();
            });
    }

    public static function addNote(): Action
    {
        return Action::make('addNote')
            ->label('Add note')
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->color('gray')
            ->authorize(fn (ContactInquiry $record): bool => Gate::allows('update', $record))
            ->modalDescription('Internal notes are visible to staff only.')
            ->schema([Textarea::make('note')->required()->rows(4)->maxLength(5000)])
            ->action(function (ContactInquiry $record, array $data): void {
                app(InquiryManager::class)->addNote(Auth::user(), $record, (string) $data['note']);
                Notification::make()->title('Note added')->success()->send();
            });
    }

    public static function scheduleFollowUp(): Action
    {
        return Action::make('scheduleFollowUp')
            ->label('Follow-up')
            ->icon('heroicon-o-calendar-days')
            ->color('gray')
            ->authorize(fn (ContactInquiry $record): bool => Gate::allows('update', $record))
            ->schema([
                DateTimePicker::make('follow_up_at')->label('Follow up on')->seconds(false)->native(false)
                    ->helperText('Leave empty to clear the follow-up.'),
                Textarea::make('note')->label('Note (optional)')->rows(2)->maxLength(2000),
            ])
            ->fillForm(fn (ContactInquiry $record): array => ['follow_up_at' => $record->follow_up_at])
            ->action(function (ContactInquiry $record, array $data): void {
                $at = filled($data['follow_up_at'] ?? null) ? Carbon::parse($data['follow_up_at']) : null;
                app(InquiryManager::class)->scheduleFollowUp(Auth::user(), $record, $at, $data['note'] ?? null);
                Notification::make()->title('Follow-up saved')->success()->send();
            });
    }

    public static function toggleSpam(): Action
    {
        return Action::make('toggleSpam')
            ->label(fn (ContactInquiry $record): string => $record->is_spam ? 'Not spam' : 'Mark as spam')
            ->icon('heroicon-o-no-symbol')
            ->color('danger')
            ->requiresConfirmation()
            ->authorize(fn (ContactInquiry $record): bool => Gate::allows('update', $record))
            ->action(function (ContactInquiry $record): void {
                app(InquiryManager::class)->markSpam(Auth::user(), $record, ! $record->is_spam);
                Notification::make()->title('Inquiry updated')->success()->send();
            });
    }

    public static function emailClient(): Action
    {
        return Action::make('emailClient')
            ->label('Reply by email')
            ->icon('heroicon-o-envelope')
            ->color('gray')
            ->url(fn (ContactInquiry $record): string => 'mailto:'.rawurlencode($record->email).'?subject='.rawurlencode('Re: '.($record->subject ?: 'Your inquiry').' ['.$record->reference.']'))
            ->authorize(fn (ContactInquiry $record): bool => Gate::allows('view', $record));
    }

    public static function downloadAttachment(): Action
    {
        return Action::make('downloadAttachment')
            ->label('Attachment')
            ->icon('heroicon-o-paper-clip')
            ->color('gray')
            ->url(fn (ContactInquiry $record): string => route('files.inquiry-attachment', $record), shouldOpenInNewTab: true)
            ->authorize(fn (ContactInquiry $record): bool => Gate::allows('downloadAttachment', $record))
            ->visible(fn (ContactInquiry $record): bool => filled($record->attachment_path));
    }

    public static function bulkAssign(): BulkAction
    {
        return BulkAction::make('bulkAssign')
            ->label('Assign')
            ->icon('heroicon-o-user-plus')
            ->authorize(fn (): bool => Gate::allows('assign', ContactInquiry::class))
            ->schema([self::assigneeSelect()])
            ->action(fn (Collection $records, array $data) => self::eachRecord($records, fn (InquiryManager $m, ContactInquiry $r) => $m->assign(Auth::user(), $r, self::resolveUser($data['assigned_to'] ?? null))))
            ->deselectRecordsAfterCompletion();
    }

    public static function bulkStatus(): BulkAction
    {
        return BulkAction::make('bulkStatus')
            ->label('Change status')
            ->icon('heroicon-o-arrow-path')
            ->authorize(fn (): bool => (bool) Auth::user()?->can('inquiries.update'))
            ->schema([Select::make('status')->options(InquiryStatus::class)->required()])
            ->action(function (Collection $records, array $data): void {
                $status = $data['status'] instanceof InquiryStatus ? $data['status'] : InquiryStatus::from($data['status']);
                self::eachRecord($records, fn (InquiryManager $m, ContactInquiry $r) => $m->changeStatus(Auth::user(), $r, $status));
            })
            ->deselectRecordsAfterCompletion();
    }

    public static function bulkSpam(): BulkAction
    {
        return BulkAction::make('bulkSpam')
            ->label('Mark as spam')
            ->icon('heroicon-o-no-symbol')
            ->color('danger')
            ->requiresConfirmation()
            ->authorize(fn (): bool => (bool) Auth::user()?->can('inquiries.update'))
            ->action(fn (Collection $records) => self::eachRecord($records, fn (InquiryManager $m, ContactInquiry $r) => $m->markSpam(Auth::user(), $r)))
            ->deselectRecordsAfterCompletion();
    }

    public static function bulkExport(): BulkAction
    {
        return BulkAction::make('exportSelected')
            ->label('Export selected (CSV)')
            ->icon('heroicon-o-arrow-down-tray')
            ->authorize(fn (): bool => Gate::allows('export', ContactInquiry::class))
            ->action(fn (Collection $records): StreamedResponse => self::export(
                $records->filter(fn (ContactInquiry $record) => Gate::allows('view', $record))->load(['service', 'assignee', 'team']),
                'selection',
            ));
    }

    /**
     * Exports the currently filtered table query.
     */
    public static function exportFiltered(): Action
    {
        return Action::make('exportCsv')
            ->label('Export CSV')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->authorize(fn (): bool => Gate::allows('export', ContactInquiry::class))
            ->action(function ($livewire): StreamedResponse {
                /** @var Builder $query */
                $query = $livewire->getFilteredSortedTableQuery();

                return self::export($query->with(['service', 'assignee', 'team'])->limit(10000)->get(), 'filtered');
            });
    }

    /**
     * @param  Collection<int, ContactInquiry>  $records
     */
    public static function export(Collection $records, string $scope): StreamedResponse
    {
        app(AuditLogger::class)->log('inquiries.exported', description: 'Exported '.$records->count().' inquiries to CSV ('.$scope.')', new: ['count' => $records->count(), 'scope' => $scope]);

        return CsvExport::download(
            'inquiries-'.now()->format('Y-m-d-His').'.csv',
            ['Reference', 'Received', 'Type', 'Topic', 'Team', 'Status', 'Priority', 'Name', 'Email', 'Phone', 'Company', 'Service', 'Budget', 'Timeline', 'Subject', 'Message', 'Assigned to', 'First response', 'Follow-up', 'Spam'],
            $records->map(fn (ContactInquiry $inquiry): array => [
                $inquiry->reference,
                $inquiry->created_at,
                $inquiry->type->getLabel(),
                $inquiry->topic?->getLabel(),
                $inquiry->team?->name,
                $inquiry->status->getLabel(),
                $inquiry->priority->getLabel(),
                $inquiry->name,
                $inquiry->email,
                $inquiry->phone,
                $inquiry->company,
                $inquiry->service?->title,
                $inquiry->budget_range,
                $inquiry->timeline,
                $inquiry->subject,
                $inquiry->message,
                $inquiry->assignee?->name,
                $inquiry->first_response_at,
                $inquiry->follow_up_at,
                $inquiry->is_spam,
            ])->all(),
        );
    }

    /**
     * Assignee options are limited server-side: team leads without
     * `inquiries.view_all` only see members of the inquiry's team.
     */
    private static function assigneeSelect(): Select
    {
        $options = fn (?ContactInquiry $record): array => app(InquiryManager::class)
            ->assignableUsers($record, Auth::user())
            ->pluck('name', 'id')
            ->all();

        return Select::make('assigned_to')
            ->label('Assign to')
            ->options($options)
            ->in(fn (?ContactInquiry $record): array => array_keys($options($record)))
            ->searchable()
            ->placeholder('Unassigned');
    }

    private static function resolveUser(mixed $id): ?User
    {
        return filled($id) ? User::query()->find($id) : null;
    }

    /**
     * @param  Collection<int, ContactInquiry>  $records
     */
    private static function eachRecord(Collection $records, \Closure $callback): void
    {
        $done = 0;

        foreach ($records as $record) {
            try {
                $callback(app(InquiryManager::class), $record);
                $done++;
            } catch (Throwable) {
                // Skipped records the current user may not change.
            }
        }

        Notification::make()->title("{$done} inquiry(s) updated")->success()->send();
    }
}
