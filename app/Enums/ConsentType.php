<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ConsentType: string implements HasLabel
{
    case PrivacyPolicy = 'privacy_policy';
    case PublishTestimonial = 'publish_testimonial';
    case DisplayIdentity = 'display_identity';
    case Marketing = 'marketing';
    case Cookies = 'cookies';
    case RecruitmentData = 'recruitment_data';

    public function getLabel(): string
    {
        return match ($this) {
            self::PrivacyPolicy => 'Privacy notice accepted',
            self::PublishTestimonial => 'Permission to publish feedback',
            self::DisplayIdentity => 'Permission to display name, company and photo',
            self::Marketing => 'Marketing communications',
            self::Cookies => 'Cookie preferences',
            self::RecruitmentData => 'Recruitment data processing',
        };
    }
}
