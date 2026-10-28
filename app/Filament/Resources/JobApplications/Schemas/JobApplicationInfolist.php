<?php

namespace App\Filament\Resources\JobApplications\Schemas;

use App\Models\JobApplication;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Cover letter')
                            ->schema([
                                TextEntry::make('cover_letter')->hiddenLabel()->prose()->placeholder('No cover letter provided.')
                                    ->extraAttributes(['class' => 'whitespace-pre-line']),
                            ]),
                        Section::make('Internal notes')
                            ->schema([
                                TextEntry::make('internal_notes')->hiddenLabel()->placeholder('No notes yet.')
                                    ->extraAttributes(['class' => 'whitespace-pre-line']),
                            ]),
                        Section::make('Consent')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                RepeatableEntry::make('consents')
                                    ->hiddenLabel()
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('consent_type')->label('Consent'),
                                        IconEntry::make('granted')->boolean(),
                                        TextEntry::make('created_at')->label('Recorded')->dateTime(),
                                    ])
                                    ->placeholder('No consent records.'),
                            ]),
                    ]),
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Application')
                            ->schema([
                                TextEntry::make('reference')->copyable()->fontFamily('mono'),
                                TextEntry::make('jobOpening.title')->label('Position')->placeholder('Open application'),
                                TextEntry::make('status')->badge(),
                                TextEntry::make('rating')->formatStateUsing(fn (?int $state): string => $state ? str_repeat('★', $state) : '—')->placeholder('—')->color('warning'),
                                TextEntry::make('reviewer.name')->label('Last reviewed by')->placeholder('—'),
                                TextEntry::make('created_at')->label('Applied')->dateTime(),
                            ]),
                        Section::make('Candidate')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('email')->copyable()->url(fn (JobApplication $record): string => 'mailto:'.$record->email),
                                TextEntry::make('phone')->placeholder('—'),
                                TextEntry::make('location')->placeholder('—'),
                                TextEntry::make('linkedin_url')->label('LinkedIn')->url(fn (JobApplication $record): ?string => $record->linkedin_url, shouldOpenInNewTab: true)->limit(40)->placeholder('—'),
                                TextEntry::make('portfolio_url')->label('Portfolio')->url(fn (JobApplication $record): ?string => $record->portfolio_url, shouldOpenInNewTab: true)->limit(40)->placeholder('—'),
                                TextEntry::make('resume_name')
                                    ->label('Résumé')
                                    ->icon('heroicon-o-paper-clip')
                                    ->url(fn (JobApplication $record): ?string => $record->resume_path ? route('files.resume', $record) : null, shouldOpenInNewTab: true)
                                    ->placeholder('—'),
                            ]),
                    ]),
            ]);
    }
}
