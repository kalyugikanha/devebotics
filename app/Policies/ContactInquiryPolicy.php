<?php

namespace App\Policies;

use App\Models\ContactInquiry;
use App\Models\User;

/**
 * Users with `inquiries.view_all` see every lead. Others see leads assigned
 * to them and, with `inquiries.view_team`, every lead routed to one of their
 * teams (record-level protection against IDOR).
 */
class ContactInquiryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny(['inquiries.view', 'inquiries.view_team', 'inquiries.view_all']);
    }

    public function view(User $user, ContactInquiry $inquiry): bool
    {
        if ($user->can('inquiries.view_all')) {
            return true;
        }

        if ($inquiry->assigned_to === $user->getKey() && $user->canAny(['inquiries.view', 'inquiries.view_team'])) {
            return true;
        }

        return $user->can('inquiries.view_team') && $user->belongsToTeam($inquiry->team_id);
    }

    /**
     * Inquiries originate only from the public forms.
     */
    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ContactInquiry $inquiry): bool
    {
        return $user->can('inquiries.update') && $this->view($user, $inquiry);
    }

    public function assign(User $user, ?ContactInquiry $inquiry = null): bool
    {
        return $user->can('inquiries.assign') && ($inquiry === null || $this->view($user, $inquiry));
    }

    /**
     * Pick up an unassigned inquiry from one of your teams' queues.
     */
    public function claim(User $user, ContactInquiry $inquiry): bool
    {
        return $inquiry->assigned_to === null
            && $user->can('inquiries.update')
            && $user->can('inquiries.view_team')
            && $user->belongsToTeam($inquiry->team_id);
    }

    /**
     * Moving an inquiry to another team crosses team boundaries, so it needs `view_all`.
     */
    public function changeTeam(User $user, ContactInquiry $inquiry): bool
    {
        return $user->can('inquiries.view_all') && $user->can('inquiries.assign');
    }

    public function export(User $user): bool
    {
        return $user->can('inquiries.export');
    }

    public function downloadAttachment(User $user, ContactInquiry $inquiry): bool
    {
        return $this->view($user, $inquiry);
    }

    public function delete(User $user, ContactInquiry $inquiry): bool
    {
        return $user->can('inquiries.delete') && $this->view($user, $inquiry);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('inquiries.delete');
    }

    public function restore(User $user, ContactInquiry $inquiry): bool
    {
        return $user->can('inquiries.delete');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('inquiries.delete');
    }

    public function forceDelete(User $user, ContactInquiry $inquiry): bool
    {
        return $user->can('inquiries.delete');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('inquiries.delete');
    }
}
