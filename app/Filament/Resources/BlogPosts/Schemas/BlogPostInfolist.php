<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BlogPostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Reviewer feedback')
                            ->icon('heroicon-o-chat-bubble-left-ellipsis')
                            ->visible(fn ($record): bool => filled($record?->review_notes))
                            ->schema([TextEntry::make('review_notes')->hiddenLabel()->prose()]),
                        Section::make('Article')
                            ->schema([
                                TextEntry::make('title')->size('lg')->weight('bold'),
                                TextEntry::make('excerpt')->label('Summary'),
                                TextEntry::make('body')->html()->prose()->hiddenLabel(),
                            ]),
                    ]),
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Status')
                            ->schema([
                                TextEntry::make('status')->badge(),
                                TextEntry::make('published_at')->label('Publish date')->dateTime('M j, Y H:i')->placeholder('—'),
                                TextEntry::make('submitted_at')->label('Submitted for review')->since()->placeholder('—'),
                                TextEntry::make('reviewer.name')->label('Reviewed by')->placeholder('Not reviewed yet'),
                                IconEntry::make('is_ai_assisted')->label('AI-assisted draft')->boolean(),
                            ]),
                        Section::make('Organisation')
                            ->schema([
                                TextEntry::make('author.name')->label('Author')->placeholder('—'),
                                TextEntry::make('category.name')->label('Category')->placeholder('—'),
                                TextEntry::make('tags.name')->label('Tags')->badge()->placeholder('—'),
                                TextEntry::make('reading_time_minutes')->label('Reading time')->suffix(' min'),
                            ]),
                    ]),
            ]);
    }
}
