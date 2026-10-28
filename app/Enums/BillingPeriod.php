<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BillingPeriod: string implements HasLabel
{
    case OneTime = 'one_time';
    case Monthly = 'monthly';
    case Yearly = 'yearly';
    case PerUserMonthly = 'per_user_monthly';
    case Custom = 'custom';

    public function getLabel(): string
    {
        return match ($this) {
            self::OneTime => 'One-time',
            self::Monthly => 'Per month',
            self::Yearly => 'Per year',
            self::PerUserMonthly => 'Per user / month',
            self::Custom => 'Custom',
        };
    }

    public function suffix(): string
    {
        return match ($this) {
            self::OneTime, self::Custom => '',
            self::Monthly => '/mo',
            self::Yearly => '/yr',
            self::PerUserMonthly => '/user/mo',
        };
    }
}
