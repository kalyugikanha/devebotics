<?php

namespace App\Filament\Resources\ContactInquiries\Schemas;

use App\Models\ContactInquiry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactInquiryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Message')
                            ->schema([
                                TextEntry::make('subject')->placeholder('—')->weight('medium'),
                                TextEntry::make('message')->hiddenLabel()->prose()->extraAttributes(['class' => 'whitespace-pre-line']),
                                TextEntry::make('attachment_name')
                                    ->label('Attachment')
                                    ->icon('heroicon-o-paper-clip')
                                    ->url(fn (ContactInquiry $record): string => route('files.inquiry-attachment', $record), shouldOpenInNewTab: true)
                                    ->visible(fn (ContactInquiry $record): bool => filled($record->attachment_path)),
                            ]),
                        Section::make('Activity')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                RepeatableEntry::make('activities')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->schema([
                                        TextEntry::make('type')->hiddenLabel()->badge(),
                                        TextEntry::make('body')->hiddenLabel()->extraAttributes(['class' => 'whitespace-pre-line'])->placeholder('—'),
                                        TextEntry::make('user.name')->hiddenLabel()->size('sm')->color('gray')->placeholder('System')
                                            ->suffix(fn ($record): string => ' · '.$record->created_at?->diffForHumans()),
                                    ])
                                    ->placeholder('No activity yet.'),
                            ]),
                        Section::make('Email history')
                            ->icon('heroicon-o-envelope')
                            ->collapsible()
                            ->schema([
                                RepeatableEntry::make('emailLogs')
                                    ->hiddenLabel()
                                    ->columns(4)
                                    ->schema([
                                        TextEntry::make('subject')->columnSpan(2),
                                        TextEntry::make('recipient_masked')->label('To'),
                                        TextEntry::make('status')->badge(),
                                    ])
                                    ->placeholder('No emails recorded.'),
                            ]),
                    ]),
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Query desk')
                            ->schema([
                                TextEntry::make('reference')->copyable()->fontFamily('mono'),
                                TextEntry::make('status')->badge(),
                                TextEntry::make('priority')->badge(),
                                TextEntry::make('topic')->badge(),
                                TextEntry::make('team.name')->label('Team')->placeholder('Unrouted'),
                                TextEntry::make('assignee.name')->label('Assigned to')->placeholder('Unassigned'),
                                TextEntry::make('first_response_at')
                                    ->label('First response')
                                    ->dateTime()
                                    ->placeholder(fn (ContactInquiry $record): string => $record->isOverdue()
                                        ? 'Overdue (was due '.$record->responseDueAt()?->diffForHumans().')'
                                        : 'Due '.$record->responseDueAt()?->toDayDateTimeString())
                                    ->color(fn (ContactInquiry $record): ?string => $record->isOverdue() ? 'danger' : null),
                                TextEntry::make('follow_up_at')->label('Follow-up')->dateTime()->placeholder('—'),
                                IconEntry::make('is_spam')->label('Spam')->boolean(),
                                TextEntry::make('created_at')->label('Received')->dateTime(),
                            ]),
                        Section::make('Contact')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('email')->copyable()->url(fn (ContactInquiry $record): string => 'mailto:'.$record->email),
                                TextEntry::make('phone')->placeholder('—')->copyable(),
                                TextEntry::make('company')->placeholder('—'),
                            ]),
                        Section::make('Project')
                            ->schema([
                                TextEntry::make('type')->badge()->color('gray'),
                                TextEntry::make('service.title')->label('Service of interest')->placeholder('—'),
                                TextEntry::make('budget_range')->label('Budget')->placeholder('—'),
                                TextEntry::make('timeline')->placeholder('—'),
                            ]),
                        Section::make('Source')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                TextEntry::make('source_url')->label('Submitted from')->placeholder('—')->limit(60),
                                KeyValueEntry::make('utm')->label('Campaign (UTM)')->placeholder('—'),
                                TextEntry::make('duplicateOf.reference')->label('Duplicate of')->placeholder('—'),
                            ]),
                    ]),
            ]);
    }
}
