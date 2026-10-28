<?php

namespace App\Services\Crm;

use App\Mail\TemplatedMail;
use App\Models\ContactInquiry;
use App\Models\Team;
use App\Models\User;
use App\Support\Mail\Outbox;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Throwable;

/**
 * Query desk alerts: the assignee (panel notification + email), a team's
 * queue (panel notification to members who can pick it up) and the team's
 * shared mailbox. Failures are reported, never thrown, so they cannot break
 * the public form or an admin action.
 */
class InquiryNotifier
{
    public function __construct(private Outbox $outbox) {}

    public function assigned(ContactInquiry $inquiry, User $assignee, ?User $actor = null): void
    {
        if ($actor !== null && $assignee->is($actor)) {
            return;
        }

        $this->safely(function () use ($inquiry, $assignee): void {
            $url = $this->adminUrl($inquiry);

            Notification::make()
                ->title('Inquiry '.$inquiry->reference.' assigned to you')
                ->body($inquiry->name.($inquiry->company ? ' · '.$inquiry->company : ''))
                ->icon('heroicon-o-user-plus')
                ->actions($url ? [Action::make('open')->label('Open inquiry')->url($url)->markAsRead()] : [])
                ->sendToDatabase($assignee);

            $this->outbox->send($assignee->email, new TemplatedMail('inquiry_assigned', $this->variables($inquiry) + [
                'assignee_name' => $assignee->name,
            ], $url), $inquiry);
        });
    }

    /**
     * Tells team members who can pick up work that an unassigned inquiry is waiting.
     */
    public function queued(ContactInquiry $inquiry, Team $team): void
    {
        $this->safely(function () use ($inquiry, $team): void {
            $url = $this->adminUrl($inquiry);

            $team->members()->where('users.is_active', true)->get()
                ->filter(fn (User $member): bool => $member->can('inquiries.view_team'))
                ->each(fn (User $member) => Notification::make()
                    ->title('New inquiry in '.$team->name)
                    ->body($inquiry->reference.' · '.$inquiry->name.' — waiting to be picked up')
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->actions($url ? [Action::make('open')->label('Open inquiry')->url($url)->markAsRead()] : [])
                    ->sendToDatabase($member));
        });
    }

    public function teamMailbox(ContactInquiry $inquiry, Team $team): void
    {
        if (blank($team->email)) {
            return;
        }

        $inquiry->loadMissing('service');

        $this->safely(fn () => $this->outbox->send($team->email, new TemplatedMail('inquiry_notification', [
            'reference' => $inquiry->reference,
            'type' => $inquiry->type->getLabel(),
            'name' => $inquiry->name,
            'company' => $inquiry->company,
            'service' => $inquiry->service?->title,
            'budget' => $inquiry->budget_range,
            'timeline' => $inquiry->timeline,
            'subject' => $inquiry->subject,
            'message_excerpt' => $inquiry->excerpt(400),
        ], $this->adminUrl($inquiry)), $inquiry));
    }

    /**
     * @return array<string, string|null>
     */
    private function variables(ContactInquiry $inquiry): array
    {
        $inquiry->loadMissing('team');

        return [
            'reference' => $inquiry->reference,
            'team' => $inquiry->team?->name ?? 'Not routed',
            'topic' => $inquiry->topic?->getLabel(),
            'name' => $inquiry->name,
            'company' => $inquiry->company,
            'subject' => $inquiry->subject ?: Str::limit($inquiry->message, 60),
            'message_excerpt' => $inquiry->excerpt(400),
            'response_due' => $inquiry->responseDueAt()?->toDayDateTimeString(),
        ];
    }

    private function adminUrl(ContactInquiry $inquiry): ?string
    {
        return rescue(fn () => route('filament.admin.resources.contact-inquiries.view', $inquiry), null, false);
    }

    private function safely(callable $callback): void
    {
        try {
            $callback();
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
