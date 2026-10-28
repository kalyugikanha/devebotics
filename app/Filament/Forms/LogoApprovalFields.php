<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

/**
 * Records permission to display an organisation's logo publicly. Logos are
 * only shown on the website once approval and a reference are recorded.
 */
final class LogoApprovalFields
{
    public static function section(string $noun): Section
    {
        return Section::make('Public logo approval')
            ->description("The {$noun}'s logo is shown on the website only when usage is approved and the approval is documented (e.g. signed agreement or email reference).")
            ->icon('heroicon-o-shield-check')
            ->columns(2)
            ->schema([
                Toggle::make('logo_usage_approved')
                    ->label('Logo usage approved')
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set, ?bool $state): void {
                        if ($state && blank($get('logo_approved_at'))) {
                            $set('logo_approved_at', now()->toDateTimeString());
                        }
                    })
                    ->columnSpanFull(),
                DateTimePicker::make('logo_approved_at')
                    ->label('Approved on')
                    ->seconds(false)
                    ->native(false)
                    ->required(fn (Get $get): bool => (bool) $get('logo_usage_approved')),
                TextInput::make('approval_reference')
                    ->label('Approval reference')
                    ->maxLength(250)
                    ->placeholder('Email from J. Smith, 12 Mar 2026')
                    ->required(fn (Get $get): bool => (bool) $get('logo_usage_approved')),
            ]);
    }
}
