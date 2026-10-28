<?php

namespace App\Filament\Resources\Partners\Schemas;

use App\Enums\PartnerType;
use App\Filament\Forms\DemoToggle;
use App\Filament\Forms\LogoApprovalFields;
use App\Filament\Forms\MediaPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Partner')
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')->required()->maxLength(180)->columnSpanFull(),
                                Select::make('type')->label('Partnership type')->options(PartnerType::class)->default(PartnerType::Technology->value)->required(),
                                TextInput::make('website_url')->label('Website')->url()->maxLength(2048),
                                Textarea::make('description')->rows(3)->maxLength(1000)->columnSpanFull(),
                            ]),
                        LogoApprovalFields::section('partner'),
                    ]),
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Display')
                            ->schema([
                                MediaPicker::make('logo_media_id', 'logo', 'Logo'),
                                Toggle::make('is_active')->label('Active')->default(true),
                                DemoToggle::make(),
                            ]),
                    ]),
            ]);
    }
}
