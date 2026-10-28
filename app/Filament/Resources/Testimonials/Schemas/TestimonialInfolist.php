<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Models\Testimonial;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class TestimonialInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $stars = fn (?int $state): string => $state ? str_repeat('★', $state).str_repeat('☆', 5 - $state) : '—';

        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Feedback')
                            ->schema([
                                Grid::make(4)->schema([
                                    TextEntry::make('rating')->label('Overall')->formatStateUsing($stars)->color('warning'),
                                    TextEntry::make('rating_communication')->label('Communication')->formatStateUsing($stars)->placeholder('—'),
                                    TextEntry::make('rating_quality')->label('Quality')->formatStateUsing($stars)->placeholder('—'),
                                    TextEntry::make('rating_delivery')->label('Delivery')->formatStateUsing($stars)->placeholder('—'),
                                ]),
                                TextEntry::make('feedback')->hiddenLabel()->prose(),
                            ]),
                        Section::make('Consent records')
                            ->description('Auditable trail of the choices made when the feedback was submitted.')
                            ->collapsible()
                            ->schema([
                                RepeatableEntry::make('consents')
                                    ->hiddenLabel()
                                    ->columns(4)
                                    ->schema([
                                        TextEntry::make('consent_type')->label('Consent'),
                                        IconEntry::make('granted')->boolean(),
                                        TextEntry::make('policy_version')->label('Policy version')->placeholder('—'),
                                        TextEntry::make('created_at')->label('Recorded')->dateTime(),
                                    ])
                                    ->placeholder('No consent records (entered by staff).'),
                            ]),
                    ]),
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Client')
                            ->schema([
                                TextEntry::make('client_name'),
                                TextEntry::make('company_name')->placeholder('—'),
                                TextEntry::make('job_title')->placeholder('—'),
                                TextEntry::make('email')
                                    ->copyable()
                                    ->placeholder('—')
                                    ->visible(fn (): bool => (bool) Auth::user()?->can('testimonials.moderate')),
                                TextEntry::make('service.title')->label('Service')->placeholder('—'),
                                TextEntry::make('project_name')->placeholder('—'),
                            ]),
                        Section::make('Moderation')
                            ->schema([
                                TextEntry::make('status')->badge(),
                                IconEntry::make('publish_permission')->label('Permission to publish')->boolean(),
                                IconEntry::make('display_identity_consent')->label('Identity consent')->boolean(),
                                TextEntry::make('public_name')->label('Shown publicly as')->state(fn (Testimonial $record): string => $record->publicName()),
                                TextEntry::make('moderator.name')->label('Moderated by')->placeholder('—'),
                                TextEntry::make('moderated_at')->dateTime()->placeholder('—'),
                                TextEntry::make('moderation_note')->label('Note')->placeholder('—')
                                    ->visible(fn (): bool => (bool) Auth::user()?->can('testimonials.moderate')),
                                TextEntry::make('admin_reply')->label('Private reply')->placeholder('—')
                                    ->visible(fn (): bool => (bool) Auth::user()?->can('testimonials.moderate')),
                            ]),
                    ]),
            ]);
    }
}
