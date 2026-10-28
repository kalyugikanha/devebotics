<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Enums\TestimonialStatus;
use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Models\Testimonial;
use Filament\Resources\Pages\CreateRecord;

class CreateTestimonial extends CreateRecord
{
    protected static string $resource = TestimonialResource::class;

    /**
     * Staff-entered testimonials start as pending and keep a record of the verified source.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['source'] = 'admin';
        $data['status'] = TestimonialStatus::Pending;
        $data['display_identity_consent'] = (bool) ($data['publish_permission'] ?? false) && (bool) ($data['display_identity_consent'] ?? false);

        if (filled($data['email'] ?? null)) {
            $data['email_hash'] = Testimonial::hashEmail((string) $data['email']);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
