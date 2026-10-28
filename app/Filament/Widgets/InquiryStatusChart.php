<?php

namespace App\Filament\Widgets;

use App\Enums\InquiryStatus;
use App\Models\ContactInquiry;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class InquiryStatusChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Lead pipeline';

    protected ?string $maxHeight = '260px';

    public static function canView(): bool
    {
        return (bool) Auth::user()?->canAny(['inquiries.view', 'inquiries.view_team', 'inquiries.view_all']);
    }

    protected function getData(): array
    {
        /** @var User $user */
        $user = Auth::user();

        $counts = ContactInquiry::query()->visibleTo($user)->where('is_spam', false)
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $statuses = InquiryStatus::cases();

        return [
            'datasets' => [[
                'label' => 'Inquiries',
                'data' => array_map(fn (InquiryStatus $status) => (int) ($counts[$status->value] ?? 0), $statuses),
                'backgroundColor' => array_map(fn (InquiryStatus $status) => $status->chartColor(), $statuses),
                'borderWidth' => 0,
            ]],
            'labels' => array_map(fn (InquiryStatus $status) => $status->getLabel(), $statuses),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['position' => 'bottom']],
            'scales' => ['x' => ['display' => false], 'y' => ['display' => false]],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
