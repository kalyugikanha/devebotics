<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactInquiries\ContactInquiryResource;
use App\Models\ContactInquiry;
use App\Models\TeamMembership;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * The signed-in user's query desk at a glance: their open inquiries, work
 * waiting in their teams' queues and overdue first responses.
 */
class QueryDeskOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected ?string $pollingInterval = '60s';

    protected ?string $heading = 'My query desk';

    public static function canView(): bool
    {
        return (bool) Auth::user()?->canAny(['inquiries.view', 'inquiries.view_team', 'inquiries.view_all']);
    }

    protected function getStats(): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user === null) {
            return [];
        }

        $visible = fn () => ContactInquiry::query()->visibleTo($user)->where('is_spam', false);
        $url = fn (string $tab): string => ContactInquiryResource::getUrl('index', ['tab' => $tab]);

        $stats = [
            Stat::make('My open inquiries', $visible()->open()->where('assigned_to', $user->getKey())->count())
                ->description('Assigned to you')
                ->descriptionIcon('heroicon-m-user')
                ->color('primary')
                ->url($url('mine')),
        ];

        if ($user->can('inquiries.view_team') && TeamMembership::query()->where('user_id', $user->getKey())->exists()) {
            $waiting = $visible()->open()->inTeamQueueOf($user)->count();

            $stats[] = Stat::make('Waiting in my teams', $waiting)
                ->description($waiting > 0 ? 'Unassigned — pick one up' : 'Team queues are clear')
                ->descriptionIcon('heroicon-m-user-group')
                ->color($waiting > 0 ? 'warning' : 'success')
                ->url($url('team'));
        }

        $overdue = $visible()->overdue()->count();

        $stats[] = Stat::make('Overdue first responses', $overdue)
            ->description('Target: first response within '.ContactInquiry::responseTargetHours().' h')
            ->descriptionIcon('heroicon-m-clock')
            ->color($overdue > 0 ? 'danger' : 'success')
            ->url($url('overdue'));

        return $stats;
    }
}
