<?php

namespace App\Filament\Resources\ContactInquiries\Pages;

use App\Enums\InquiryStatus;
use App\Filament\Resources\ContactInquiries\Actions\InquiryActions;
use App\Filament\Resources\ContactInquiries\ContactInquiryResource;
use App\Models\TeamMembership;
use App\Models\User;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Query desk queues. Every tab is applied on top of the record-level
 * visibility scope, and tabs a user cannot use are not shown.
 */
class ListContactInquiries extends ListRecords
{
    protected static string $resource = ContactInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [InquiryActions::exportFiltered()];
    }

    public function getTabs(): array
    {
        /** @var User $user */
        $user = Auth::user();
        $visible = fn (): Builder => ContactInquiryResource::getEloquentQuery()->where('is_spam', false);
        $open = InquiryStatus::values(InquiryStatus::open());
        $inTeams = $user->can('inquiries.view_team') && TeamMembership::query()->where('user_id', $user->getKey())->exists();

        $tabs = [
            'open' => Tab::make('All open')
                ->icon('heroicon-o-inbox-stack')
                ->badge(fn (): int => $visible()->whereIn('status', $open)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', $open)),
            'mine' => Tab::make('My queue')
                ->icon('heroicon-o-user')
                ->badge(fn (): int => $visible()->where('assigned_to', $user->getKey())->whereIn('status', $open)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('assigned_to', $user->getKey())->whereIn('status', $open)),
        ];

        if ($inTeams) {
            $tabs['team'] = Tab::make('Team queue')
                ->icon('heroicon-o-user-group')
                ->badge(fn (): int => $visible()->inTeamQueueOf($user)->whereIn('status', $open)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->inTeamQueueOf($user)->whereIn('status', $open));
        }

        if ($user->can('inquiries.view_all')) {
            $tabs['unassigned'] = Tab::make('Unassigned')
                ->icon('heroicon-o-question-mark-circle')
                ->badge(fn (): int => $visible()->whereNull('assigned_to')->whereIn('status', $open)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('assigned_to')->whereIn('status', $open));
        }

        return $tabs + [
            'overdue' => Tab::make('Overdue')
                ->icon('heroicon-o-clock')
                ->badge(fn (): int => $visible()->overdue()->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->overdue()),
            'follow_up' => Tab::make('Follow-up due')
                ->icon('heroicon-o-calendar-days')
                ->badge(fn (): int => $visible()->whereNotNull('follow_up_at')->where('follow_up_at', '<=', now())->whereIn('status', $open)->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('follow_up_at')->where('follow_up_at', '<=', now())->whereIn('status', $open)),
            'closed' => Tab::make('Closed')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->closed()),
            'all' => Tab::make('All'),
        ];
    }
}
