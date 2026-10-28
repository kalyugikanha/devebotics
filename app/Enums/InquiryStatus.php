<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InquiryStatus: string implements HasColor, HasLabel
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Contacted = 'contacted';
    case Qualified = 'qualified';
    case Proposal = 'proposal';
    case OnHold = 'on_hold';
    case Converted = 'converted';
    case Resolved = 'resolved';
    case Lost = 'lost';
    case Closed = 'closed';

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'New',
            self::InProgress => 'In progress',
            self::Contacted => 'Contacted',
            self::Qualified => 'Qualified',
            self::Proposal => 'Proposal sent',
            self::OnHold => 'On hold',
            self::Converted => 'Won / converted',
            self::Resolved => 'Resolved',
            self::Lost => 'Lost',
            self::Closed => 'Closed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'info',
            self::InProgress, self::Contacted => 'primary',
            self::Qualified, self::Proposal => 'warning',
            self::OnHold => 'gray',
            self::Converted, self::Resolved => 'success',
            self::Lost => 'danger',
            self::Closed => 'gray',
        };
    }

    /**
     * Chart colour for the pipeline widget.
     */
    public function chartColor(): string
    {
        return match ($this) {
            self::New => '#38bdf8',
            self::InProgress => '#6366f1',
            self::Contacted => '#8b5cf6',
            self::Qualified => '#f59e0b',
            self::Proposal => '#f97316',
            self::OnHold => '#cbd5e1',
            self::Converted => '#10b981',
            self::Resolved => '#14b8a6',
            self::Lost => '#f43f5e',
            self::Closed => '#94a3b8',
        };
    }

    public function isClosed(): bool
    {
        return in_array($this, self::closed(), true);
    }

    /**
     * @return array<self>
     */
    public static function open(): array
    {
        return [self::New, self::InProgress, self::Contacted, self::Qualified, self::Proposal, self::OnHold];
    }

    /**
     * @return array<self>
     */
    public static function closed(): array
    {
        return [self::Converted, self::Resolved, self::Lost, self::Closed];
    }

    /**
     * @param  array<self>  $statuses
     * @return array<int, string>
     */
    public static function values(array $statuses): array
    {
        return array_map(fn (self $status): string => $status->value, $statuses);
    }
}
