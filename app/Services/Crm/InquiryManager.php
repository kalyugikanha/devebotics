<?php

namespace App\Services\Crm;

use App\Enums\InquiryActivityType;
use App\Enums\InquiryPriority;
use App\Enums\InquiryStatus;
use App\Models\ContactInquiry;
use App\Models\Team;
use App\Models\TeamMembership;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

/**
 * CRM-lite operations on inquiries. Every change is authorised against the
 * policy and recorded on the inquiry's activity timeline.
 */
class InquiryManager
{
    public function __construct(
        private InquiryRouter $router,
        private InquiryNotifier $notifier,
    ) {}

    /**
     * Active users allowed to work on inquiries. Without `inquiries.view_all`
     * the actor (a team lead) may only pick members of the inquiry's team, or
     * of their own teams when no inquiry is given (bulk actions).
     *
     * @return Collection<int, User>
     */
    public function assignableUsers(?ContactInquiry $inquiry = null, ?User $actor = null): Collection
    {
        $query = User::query()->active()->orderBy('name');

        if ($actor !== null && ! $actor->can('inquiries.view_all')) {
            $teamIds = $inquiry !== null
                ? array_filter([$inquiry->team_id])
                : TeamMembership::query()->where('user_id', $actor->getKey())->pluck('team_id')->all();

            if ($teamIds === []) {
                return collect();
            }

            $query->whereIn('id', TeamMembership::query()->select('user_id')->whereIn('team_id', $teamIds));
        }

        return $query->get()
            ->filter(fn (User $user) => $user->canAny(['inquiries.view', 'inquiries.view_team', 'inquiries.view_all']))
            ->values();
    }

    public function assign(User $actor, ContactInquiry $inquiry, ?User $assignee): void
    {
        Gate::forUser($actor)->authorize('assign', $inquiry);

        if ($assignee !== null && ! $this->assignableUsers($inquiry, $actor)->contains(fn (User $user) => $user->is($assignee))) {
            throw ValidationException::withMessages(['assigned_to' => $actor->can('inquiries.view_all')
                ? 'This user cannot be assigned inquiries.'
                : 'You can only assign inquiries to members of this inquiry’s team.']);
        }

        if ($inquiry->assigned_to === $assignee?->getKey()) {
            return;
        }

        $previous = $inquiry->loadMissing('assignee')->assignee?->name;

        DB::transaction(function () use ($actor, $inquiry, $assignee, $previous): void {
            $inquiry->forceFill(['assigned_to' => $assignee?->getKey()])->save();

            if ($assignee !== null) {
                $this->router->recordAssignment($inquiry->team_id, $assignee);
            }

            $this->log($inquiry, $actor, InquiryActivityType::Assigned, $assignee
                ? 'Assigned to '.$assignee->name.($previous ? ' (previously '.$previous.')' : '')
                : 'Unassigned (previously '.($previous ?? 'nobody').')', ['from' => $previous, 'to' => $assignee?->name]);
        });

        if ($assignee !== null) {
            $this->notifier->assigned($inquiry, $assignee, $actor);
        }
    }

    /**
     * Picks up an unassigned inquiry from one of the actor's team queues.
     * The row is locked so two people cannot claim the same inquiry.
     */
    public function claim(User $actor, ContactInquiry $inquiry): void
    {
        Gate::forUser($actor)->authorize('claim', $inquiry);

        DB::transaction(function () use ($actor, $inquiry): void {
            $current = ContactInquiry::query()->whereKey($inquiry->getKey())->lockForUpdate()->value('assigned_to');

            if ($current !== null) {
                throw ValidationException::withMessages(['assigned_to' => 'Someone else has already picked up this inquiry.']);
            }

            $inquiry->forceFill(['assigned_to' => $actor->getKey()])->save();
            $this->router->recordAssignment($inquiry->team_id, $actor);
            $this->log($inquiry, $actor, InquiryActivityType::Assigned, 'Picked up from the team queue by '.$actor->name, ['to' => $actor->name]);
        });
    }

    /**
     * Moves an inquiry to another team (or back to the unrouted pool). The
     * assignee is kept only if they belong to the new team.
     */
    public function moveToTeam(User $actor, ContactInquiry $inquiry, ?Team $team): void
    {
        Gate::forUser($actor)->authorize('changeTeam', $inquiry);

        if ($inquiry->team_id === $team?->getKey()) {
            return;
        }

        $inquiry->loadMissing(['team', 'assignee']);
        $previous = $inquiry->team?->name;
        $keepAssignee = $team !== null && $inquiry->assignee !== null && $inquiry->assignee->belongsToTeam($team->getKey());

        DB::transaction(function () use ($actor, $inquiry, $team, $previous, $keepAssignee): void {
            $inquiry->forceFill([
                'team_id' => $team?->getKey(),
                'assigned_to' => $keepAssignee ? $inquiry->assigned_to : null,
            ])->save();

            $this->log($inquiry, $actor, InquiryActivityType::Routed, ($team ? 'Moved to '.$team->name : 'Removed from team').' (previously '.($previous ?? 'unrouted').')'
                .($keepAssignee ? '' : '; assignment cleared'), ['from' => $previous, 'to' => $team?->name]);
        });

        if ($team !== null) {
            $inquiry->setRelation('team', $team);

            if ($inquiry->assigned_to === null) {
                $this->notifier->queued($inquiry, $team);
            }
        }
    }

    public function changeStatus(User $actor, ContactInquiry $inquiry, InquiryStatus $status, ?string $note = null): void
    {
        Gate::forUser($actor)->authorize('update', $inquiry);

        if ($inquiry->status === $status) {
            return;
        }

        $from = $inquiry->status;
        $inquiry->forceFill(['status' => $status])->save();

        $this->log($inquiry, $actor, InquiryActivityType::StatusChanged, $from->getLabel().' → '.$status->getLabel().($note ? "\n".$note : ''), ['from' => $from->value, 'to' => $status->value]);
    }

    public function changePriority(User $actor, ContactInquiry $inquiry, InquiryPriority $priority): void
    {
        Gate::forUser($actor)->authorize('update', $inquiry);

        if ($inquiry->priority === $priority) {
            return;
        }

        $from = $inquiry->priority;
        $inquiry->forceFill(['priority' => $priority])->save();

        $this->log($inquiry, $actor, InquiryActivityType::PriorityChanged, $from->getLabel().' → '.$priority->getLabel(), ['from' => $from->value, 'to' => $priority->value]);
    }

    public function addNote(User $actor, ContactInquiry $inquiry, string $note): void
    {
        Gate::forUser($actor)->authorize('update', $inquiry);

        $this->log($inquiry, $actor, InquiryActivityType::Note, trim($note));
    }

    public function scheduleFollowUp(User $actor, ContactInquiry $inquiry, ?CarbonInterface $at, ?string $note = null): void
    {
        Gate::forUser($actor)->authorize('update', $inquiry);

        $inquiry->forceFill(['follow_up_at' => $at])->save();

        $this->log($inquiry, $actor, InquiryActivityType::FollowUpScheduled, $at
            ? 'Follow-up on '.$at->format('M j, Y H:i').($note ? "\n".$note : '')
            : 'Follow-up cleared');
    }

    public function markSpam(User $actor, ContactInquiry $inquiry, bool $isSpam = true): void
    {
        Gate::forUser($actor)->authorize('update', $inquiry);

        $inquiry->forceFill(['is_spam' => $isSpam])->save();

        $this->log($inquiry, $actor, InquiryActivityType::Note, $isSpam ? 'Marked as spam' : 'Marked as not spam');
    }

    /**
     * @param  array<string, mixed>|null  $meta
     */
    private function log(ContactInquiry $inquiry, User $actor, InquiryActivityType $type, string $body, ?array $meta = null): void
    {
        $inquiry->activities()->create([
            'user_id' => $actor->getKey(),
            'type' => $type,
            'body' => $body,
            'meta' => $meta,
            'created_at' => now(),
        ]);
    }
}
