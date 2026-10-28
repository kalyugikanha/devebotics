<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Resources\Testimonials\Actions\TestimonialActions;
use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Models\Testimonial;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTestimonial extends EditRecord
{
    protected static string $resource = TestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            ActionGroup::make(TestimonialActions::all())->label('Moderate')->icon('heroicon-m-shield-check')->button()->color('gray'),
            ActionGroup::make([DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make()]),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Identity can never be displayed without permission to publish.
        if (array_key_exists('publish_permission', $data) && ! $data['publish_permission']) {
            $data['display_identity_consent'] = false;
        }

        if (array_key_exists('email', $data)) {
            $data['email_hash'] = filled($data['email']) ? Testimonial::hashEmail((string) $data['email']) : null;
        }

        return $data;
    }
}
