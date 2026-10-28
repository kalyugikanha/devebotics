<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EmploymentType: string implements HasLabel
{
    case FullTime = 'full_time';
    case PartTime = 'part_time';
    case Contract = 'contract';
    case Internship = 'internship';
    case Temporary = 'temporary';

    public function getLabel(): string
    {
        return match ($this) {
            self::FullTime => 'Full-time',
            self::PartTime => 'Part-time',
            self::Contract => 'Contract',
            self::Internship => 'Internship',
            self::Temporary => 'Temporary',
        };
    }

    /**
     * Value expected by schema.org JobPosting.employmentType.
     */
    public function schemaOrgValue(): string
    {
        return match ($this) {
            self::FullTime => 'FULL_TIME',
            self::PartTime => 'PART_TIME',
            self::Contract => 'CONTRACTOR',
            self::Internship => 'INTERN',
            self::Temporary => 'TEMPORARY',
        };
    }
}
