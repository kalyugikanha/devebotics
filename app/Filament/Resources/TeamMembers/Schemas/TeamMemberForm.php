<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use App\Filament\Forms\DemoToggle;
use App\Filament\Forms\MediaPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Profile')
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')->required()->maxLength(120),
                                TextInput::make('job_title')->required()->maxLength(120),
                                TextInput::make('department')->maxLength(120)->datalist(['Leadership', 'Engineering', 'AI & Data', 'Design', 'Cloud & DevOps', 'Sales', 'Operations']),
                                Toggle::make('is_leadership')->label('Leadership team')->inline(false),
                                Textarea::make('bio')->rows(4)->maxLength(1200)->columnSpanFull(),
                            ]),
                        Section::make('Profiles')
                            ->columns(3)
                            ->collapsible()
                            ->schema([
                                TextInput::make('linkedin_url')->label('LinkedIn')->url()->maxLength(2048)->prefixIcon('heroicon-o-link'),
                                TextInput::make('github_url')->label('GitHub')->url()->maxLength(2048)->prefixIcon('heroicon-o-link'),
                                TextInput::make('x_url')->label('X (Twitter)')->url()->maxLength(2048)->prefixIcon('heroicon-o-link'),
                            ]),
                    ]),
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Display')
                            ->schema([
                                MediaPicker::make('photo_media_id', 'photo', 'Photo')->helperText('Only publish photos with the person’s consent.'),
                                Toggle::make('is_active')->label('Show on website')->default(true),
                                DemoToggle::make(),
                            ]),
                    ]),
            ]);
    }
}
