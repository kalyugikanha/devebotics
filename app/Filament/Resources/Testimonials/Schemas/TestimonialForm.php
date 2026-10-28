<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Filament\Forms\MediaPicker;
use App\Models\Testimonial;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        $stars = [5 => '★★★★★ (5)', 4 => '★★★★ (4)', 3 => '★★★ (3)', 2 => '★★ (2)', 1 => '★ (1)'];

        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Client')
                            ->columns(2)
                            ->schema([
                                TextInput::make('client_name')->required()->maxLength(120),
                                TextInput::make('company_name')->maxLength(160),
                                TextInput::make('job_title')->maxLength(120),
                                TextInput::make('email')
                                    ->email()
                                    ->maxLength(190)
                                    ->helperText('Private — never shown on the website.'),
                                Select::make('service_id')->label('Service')->relationship('service', 'title')->searchable()->preload(),
                                Select::make('product_id')->label('Product')->relationship('product', 'name')->searchable()->preload(),
                                TextInput::make('project_name')->maxLength(160)->columnSpanFull(),
                            ]),
                        Section::make('Feedback')
                            ->schema([
                                Grid::make(4)->schema([
                                    Select::make('rating')->label('Overall')->options($stars)->required()->default(5),
                                    Select::make('rating_communication')->label('Communication')->options($stars),
                                    Select::make('rating_quality')->label('Quality')->options($stars),
                                    Select::make('rating_delivery')->label('Delivery')->options($stars),
                                ]),
                                Textarea::make('feedback')->required()->rows(6)->minLength(20)->maxLength(3000),
                            ]),
                    ]),
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Consent')
                            ->description('Feedback can only be published with the client’s permission.')
                            ->schema([
                                Toggle::make('publish_permission')->label('Client permits publishing this feedback')->live(),
                                Toggle::make('display_identity_consent')
                                    ->label('Client permits showing name, company and photo')
                                    ->disabled(fn (Get $get): bool => ! $get('publish_permission')),
                                MediaPicker::make('photo_media_id', 'photo', 'Photo or logo')
                                    ->visible(fn (Get $get): bool => (bool) $get('display_identity_consent')),
                            ]),
                        Section::make('Verification')
                            ->schema([
                                Textarea::make('moderation_note')
                                    ->label(fn (string $operation): string => $operation === 'create' ? 'Verified source' : 'Internal moderation note')
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->rows(3)
                                    ->maxLength(1000)
                                    ->helperText(fn (string $operation): ?string => $operation === 'create'
                                        ? 'Required: where and when the client gave this feedback and their permission (e.g. signed email of 12 May 2026). Fabricated reviews are not allowed.'
                                        : null),
                            ]),
                        Section::make('Status')
                            ->hiddenOn('create')
                            ->schema([
                                TextEntry::make('status')->badge(),
                                TextEntry::make('source')->formatStateUsing(fn (?string $state): string => $state === 'admin' ? 'Entered by staff' : 'Client feedback form'),
                                TextEntry::make('moderator.name')->label('Moderated by')->placeholder('—'),
                                TextEntry::make('email_verified_at')->label('Email verified')->dateTime()->placeholder('Not verified'),
                                TextEntry::make('admin_reply')->label('Private reply')->placeholder('—')
                                    ->visible(fn (?Testimonial $record): bool => filled($record?->admin_reply)),
                            ]),
                    ]),
            ]);
    }
}
