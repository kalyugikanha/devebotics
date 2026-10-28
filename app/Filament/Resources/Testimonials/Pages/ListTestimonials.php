<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Enums\TestimonialStatus;
use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Models\Testimonial;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTestimonials extends ListRecords
{
    protected static string $resource = TestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Add verified testimonial'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];

        foreach (TestimonialStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->getLabel())
                ->badge(fn (): int => Testimonial::query()->where('status', $status->value)->count())
                ->badgeColor($status->getColor())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status->value));
        }

        return $tabs + ['all' => Tab::make('All')];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return TestimonialStatus::Pending->value;
    }
}
