<?php

namespace App\Filament\Resources\Clients\Schemas;

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

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Client')
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')->required()->maxLength(180)->columnSpanFull(),
                                TextInput::make('website_url')->label('Website')->url()->maxLength(2048),
                                Select::make('industry_id')->label('Industry')->relationship('industry', 'name')->searchable()->preload(),
                                Textarea::make('description')->label('Internal notes / description')->rows(3)->maxLength(1000)->columnSpanFull(),
                            ]),
                        LogoApprovalFields::section('client'),
                    ]),
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Display')
                            ->schema([
                                MediaPicker::make('logo_media_id', 'logo', 'Logo'),
                                Toggle::make('show_on_home')->label('Include in homepage logo showcase')->default(true),
                                Toggle::make('is_active')->label('Active')->default(true),
                                DemoToggle::make(),
                            ]),
                    ]),
            ]);
    }
}
