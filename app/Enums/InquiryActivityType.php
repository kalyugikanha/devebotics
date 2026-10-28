<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum InquiryActivityType: string implements HasColor, HasIcon, HasLabel
{
    case Created = 'created';
    case Routed = 'routed';
    case Note = 'note';
    case StatusChanged = 'status_changed';
    case PriorityChanged = 'priority_changed';
    case Assigned = 'assigned';
    case FollowUpScheduled = 'follow_up_scheduled';
    case EmailSent = 'email_sent';

    public function getLabel(): string
    {
        return match ($this) {
            self::Created => 'Inquiry received',
            self::Routed => 'Routed to team',
            self::Note => 'Internal note',
            self::StatusChanged => 'Status changed',
            self::PriorityChanged => 'Priority changed',
            self::Assigned => 'Assignment changed',
            self::FollowUpScheduled => 'Follow-up scheduled',
            self::EmailSent => 'Email sent',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Created => 'info',
            self::Routed => 'primary',
            self::Note => 'gray',
            self::StatusChanged, self::PriorityChanged => 'warning',
            self::Assigned => 'primary',
            self::FollowUpScheduled => 'info',
            self::EmailSent => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Created => 'heroicon-o-inbox-arrow-down',
            self::Routed => 'heroicon-o-arrows-right-left',
            self::Note => 'heroicon-o-chat-bubble-left-ellipsis',
            self::StatusChanged => 'heroicon-o-arrow-path',
            self::PriorityChanged => 'heroicon-o-flag',
            self::Assigned => 'heroicon-o-user-plus',
            self::FollowUpScheduled => 'heroicon-o-calendar-days',
            self::EmailSent => 'heroicon-o-envelope',
        };
    }
}
