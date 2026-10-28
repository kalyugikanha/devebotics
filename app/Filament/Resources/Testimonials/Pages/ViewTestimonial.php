<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Resources\Testimonials\Actions\TestimonialActions;
use App\Filament\Resources\Testimonials\TestimonialResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewTestimonial extends ViewRecord
{
    protected static string $resource = TestimonialResource::class;

    protected function resolveRecord(int|string $key): Model
    {
        return parent::resolveRecord($key)->load(['consents', 'service', 'moderator']);
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make(TestimonialActions::all())->label('Moderate')->icon('heroicon-m-shield-check')->button()->color('gray'),
            EditAction::make(),
        ];
    }
}
