<?php

namespace App\Services\Crm;

use App\Enums\InquiryActivityType;
use App\Models\ContactInquiry;
use App\Models\Team;
use App\Models\TeamMembership;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Routes a new inquiry to a team and, for teams with auto-assignment,
 * to the team member with the fewest open inquiries (ties go to whoever
 * was assigned least recently).
 *
 * Team choice, in order: an active team that handles both the topic and the
 * selected service, then one that handles the topic, then one linked to the
 * service. Without a match the inquiry stays unrouted (visible to view_all).
 */
class InquiryRouter
{
    public function __construct(private InquiryNotifier $notifier) {}

    public function route(ContactInquiry $inquiry): ?Team
    {
        [$team, $reason] = $this->teamFor($inquiry);

        if ($team === null) {
            return null;
        }

        $assignee = DB::transaction(function () use ($inquiry, $team, $reason): ?User {
            $inquiry->forceFill(['team_id' => $team->getKey()])->save();
            $this->log($inquiry, InquiryActivityType::Routed, 'Routed to '.$team->name.' ('.$reason.')', ['team' => $team->name, 'reason' => $reason]);

            $assignee = $team->auto_assign ? $this->nextAssignee($team) : null;

            if ($assignee !== null) {
                $inquiry->forceFill(['assigned_to' => $assignee->getKey()])->save();
                $this->recordAssignment($team->getKey(), $assignee);
                $this->log($inquiry, InquiryActivityType::Assigned, 'Auto-assigned to '.$assignee->name.' (round-robin)', ['to' => $assignee->name]);
            }

            return $assignee;
        });

        $inquiry->setRelation('team', $team);

        if ($assignee !== null) {
            $this->notifier->assigned($inquiry, $assignee);
        } else {
            $this->notifier->queued($inquiry, $team);
        }

        $this->notifier->teamMailbox($inquiry, $team);

        return $team;
    }

    /**
     * @return array{0: Team|null, 1: string}
     */
    public function teamFor(ContactInquiry $inquiry): array
    {
        $teams = Team::query()->active()->ordered()->with('services:services.id')->get();
        $servesService = fn (Team $team): bool => $inquiry->service_id !== null && $team->services->contains('id', $inquiry->service_id);
        $handlesTopic = fn (Team $team): bool => $inquiry->topic !== null && $team->handlesTopic($inquiry->topic);

        if ($team = $teams->first(fn (Team $team): bool => $handlesTopic($team) && $servesService($team))) {
            return [$team, 'topic and service'];
        }

        if ($team = $teams->first($handlesTopic)) {
            return [$team, 'topic: '.$inquiry->topic->getLabel()];
        }

        if ($team = $teams->first($servesService)) {
            return [$team, 'service'];
        }

        return [null, ''];
    }

    /**
     * Active members able to work on inquiries, fewest open inquiries first,
     * then least recently assigned.
     */
    public function nextAssignee(Team $team): ?User
    {
        $memberships = $team->memberships()->with('user')->get()
            ->filter(fn (TeamMembership $membership): bool => $membership->user?->is_active === true
                && $membership->user->canAny(['inquiries.view', 'inquiries.view_team', 'inquiries.view_all']));

        if ($memberships->isEmpty()) {
            return null;
        }

        $openCounts = ContactInquiry::query()->open()->where('is_spam', false)
            ->whereIn('assigned_to', $memberships->pluck('user_id'))
            ->selectRaw('assigned_to, count(*) as aggregate')
            ->groupBy('assigned_to')
            ->pluck('aggregate', 'assigned_to');

        return $memberships
            ->sort(fn (TeamMembership $a, TeamMembership $b): int => [(int) ($openCounts[$a->user_id] ?? 0), $a->last_assigned_at?->getTimestamp() ?? 0, $a->id]
                <=> [(int) ($openCounts[$b->user_id] ?? 0), $b->last_assigned_at?->getTimestamp() ?? 0, $b->id])
            ->first()
            ?->user;
    }

    /**
     * Remembers when a member last received work, for fair round-robin.
     */
    public function recordAssignment(?int $teamId, User $assignee): void
    {
        if ($teamId === null) {
            return;
        }

        TeamMembership::query()->where('team_id', $teamId)->where('user_id', $assignee->getKey())->update(['last_assigned_at' => now()]);
    }

    /**
     * @param  array<string, mixed>|null  $meta
     */
    private function log(ContactInquiry $inquiry, InquiryActivityType $type, string $body, ?array $meta = null): void
    {
        $inquiry->activities()->create([
            'type' => $type,
            'body' => $body,
            'meta' => $meta,
            'created_at' => now(),
        ]);
    }
}
