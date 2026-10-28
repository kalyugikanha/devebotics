<?php

namespace App\Filament\Widgets;

use App\Models\ContactInquiry;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class InquiriesChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Inquiries over time';

    protected ?string $maxHeight = '260px';

    public ?string $filter = '30';

    public static function canView(): bool
    {
        return (bool) Auth::user()?->canAny(['inquiries.view', 'inquiries.view_team', 'inquiries.view_all']);
    }

    protected function getFilters(): ?array
    {
        return ['7' => 'Last 7 days', '30' => 'Last 30 days', '90' => 'Last 90 days'];
    }

    protected function getData(): array
    {
        /** @var User $user */
        $user = Auth::user();
        $days = in_array($this->filter, ['7', '30', '90'], true) ? (int) $this->filter : 30;
        $start = now()->subDays($days - 1)->startOfDay();

        $counts = ContactInquiry::query()
            ->visibleTo($user)
            ->where('is_spam', false)
            ->where('created_at', '>=', $start)
            ->pluck('created_at')
            ->countBy(fn (Carbon $date) => $date->toDateString());

        $labels = [];
        $values = [];

        for ($date = $start->copy(); $date->lessThanOrEqualTo(now()); $date->addDay()) {
            $labels[] = $date->format('M j');
            $values[] = $counts[$date->toDateString()] ?? 0;
        }

        return [
            'datasets' => [[
                'label' => 'Inquiries',
                'data' => $values,
                'fill' => true,
                'tension' => 0.35,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
