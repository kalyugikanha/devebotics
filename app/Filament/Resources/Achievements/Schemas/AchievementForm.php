<?php

namespace App\Filament\Resources\Achievements\Schemas;

use App\Enums\AchievementType;
use App\Filament\Forms\DemoToggle;
use App\Filament\Forms\MediaPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AchievementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Certification / award')
                            ->description('Only verified, unexpired entries are shown publicly. Never list credentials the company does not hold.')
                            ->columns(2)
                            ->schema([
                                TextInput::make('title')->required()->maxLength(180)->columnSpanFull(),
                                Select::make('type')->options(AchievementType::class)->default(AchievementType::Certification->value)->required(),
                                TextInput::make('issuer')->label('Issued by')->maxLength(180),
                                DatePicker::make('issued_on')->native(false),
                                DatePicker::make('expires_on')->native(false)->afterOrEqual('issued_on')->helperText('Expired entries are hidden automatically.'),
                                TextInput::make('verification_url')->label('Verification link')->url()->maxLength(2048)->columnSpanFull(),
                                Textarea::make('description')->rows(3)->maxLength(800)->columnSpanFull(),
                            ]),
                    ]),
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Verification & display')
                            ->schema([
                                Toggle::make('is_verified')
                                    ->label('Verified')
                                    ->helperText('Confirm you hold evidence of this credential (certificate, registry entry or award letter).'),
                                MediaPicker::make('media_id', 'media', 'Badge / logo'),
                                Toggle::make('is_active')->label('Active')->default(true),
                                DemoToggle::make(),
                            ]),
                    ]),
            ]);
    }
}
