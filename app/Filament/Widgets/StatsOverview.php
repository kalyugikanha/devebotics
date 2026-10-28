<?php

namespace App\Filament\Widgets;

use App\Enums\InquiryStatus;
use App\Enums\TestimonialStatus;
use App\Models\BlogPost;
use App\Models\ContactInquiry;
use App\Models\Product;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Key figures. Each stat is shown only to users permitted to see the module.
 */
class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return (bool) Auth::user()?->canAny([
            'inquiries.view', 'inquiries.view_team', 'inquiries.view_all', 'services.view', 'services.manage', 'products.view', 'products.manage',
            'blogs.view', 'blogs.create', 'blogs.publish', 'testimonials.view', 'testimonials.moderate',
        ]);
    }

    protected function getStats(): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user === null) {
            return [];
        }

        $stats = [];

        if ($user->canAny(['inquiries.view', 'inquiries.view_team', 'inquiries.view_all'])) {
            $inquiries = fn () => ContactInquiry::query()->visibleTo($user)->where('is_spam', false);
            $trend = collect(range(6, 0))->map(fn (int $daysAgo) => $inquiries()->whereDate('created_at', now()->subDays($daysAgo)->toDateString())->count())->all();

            $stats[] = Stat::make('New inquiries (7 days)', $inquiries()->where('created_at', '>=', now()->subDays(7))->count())
                ->description($inquiries()->where('status', InquiryStatus::New->value)->count().' awaiting first contact')
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->chart($trend)
                ->color('primary');

            $stats[] = Stat::make('Open leads', $inquiries()->open()->count())
                ->description($inquiries()->where('status', InquiryStatus::Qualified->value)->count().' qualified')
                ->descriptionIcon('heroicon-m-funnel')
                ->color('info');
        }

        if ($user->canAny(['services.view', 'services.manage'])) {
            $stats[] = Stat::make('Published services', Service::query()->published()->count())->descriptionIcon('heroicon-m-cpu-chip');
        }

        if ($user->canAny(['products.view', 'products.manage'])) {
            $stats[] = Stat::make('Published products', Product::query()->published()->count())->descriptionIcon('heroicon-m-cube');
        }

        if ($user->canAny(['blogs.view', 'blogs.create', 'blogs.publish'])) {
            $stats[] = Stat::make('Published articles', BlogPost::query()->published()->count())->descriptionIcon('heroicon-m-newspaper');
        }

        if ($user->canAny(['testimonials.view', 'testimonials.moderate'])) {
            $pending = Testimonial::query()->where('status', TestimonialStatus::Pending->value)->count();

            $stats[] = Stat::make('Pending reviews', $pending)
                ->description(Testimonial::query()->where('status', TestimonialStatus::Approved->value)->count().' approved in total')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($pending > 0 ? 'warning' : 'success');
        }

        return $stats;
    }
}
