<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Enums\ContentStatus;
use App\Filament\Forms\ContentFields;
use App\Filament\Forms\IllustrationSelect;
use App\Filament\Forms\MediaPicker;
use App\Filament\Forms\SeoSection;
use App\Models\Service;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        $locked = ContentFields::lockedUnless('services.manage');

        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Service details')
                            ->disabled($locked)
                            ->columns(2)
                            ->schema([
                                ContentFields::title()->columnSpanFull(),
                                ContentFields::slug('services', '/services'),
                                TextInput::make('tagline')->maxLength(180)->helperText('Short line shown above the title.'),
                                Select::make('service_category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([TextInput::make('name')->required()->maxLength(120)]),
                                ContentFields::icon(),
                                Textarea::make('excerpt')
                                    ->label('Card summary')
                                    ->rows(2)
                                    ->maxLength(300)
                                    ->required()
                                    ->helperText('Shown on cards and used as the default meta description.')
                                    ->columnSpanFull(),
                                Textarea::make('overview')->rows(4)->maxLength(1500)->columnSpanFull(),
                            ]),

                        Section::make('Detailed description')
                            ->disabled($locked)
                            ->collapsible()
                            ->schema([ContentFields::richEditor('description', 'Description')]),

                        Section::make('Value proposition')
                            ->disabled($locked)
                            ->collapsible()
                            ->schema([
                                Tabs::make('Lists')->tabs([
                                    Tab::make('Challenges')->icon('heroicon-o-exclamation-triangle')->schema([ContentFields::listItems('challenges', 'Business challenges addressed')]),
                                    Tab::make('Benefits')->icon('heroicon-o-check-circle')->schema([ContentFields::listItems('benefits', 'Key benefits')]),
                                    Tab::make('Features')->icon('heroicon-o-squares-2x2')->schema([ContentFields::listItems('features', 'Features & capabilities', 16)]),
                                    Tab::make('Process')->icon('heroicon-o-arrow-trending-up')->schema([ContentFields::listItems('process_steps', 'Delivery process', 8, withIcon: false)]),
                                ]),
                            ]),

                        Section::make('Relationships')
                            ->disabled($locked)
                            ->collapsible()
                            ->columns(2)
                            ->schema([
                                Select::make('technologies')
                                    ->relationship('technologies', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([TextInput::make('name')->required()->maxLength(80)]),
                                Select::make('industries')
                                    ->relationship('industries', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload(),
                                Select::make('relatedServices')
                                    ->label('Related services')
                                    ->relationship('relatedServices', 'title', fn (Builder $query, ?Service $record) => $query->when($record, fn (Builder $q) => $q->whereKeyNot($record->getKey())))
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->maxItems(6),
                                Select::make('portfolios')
                                    ->label('Relevant case studies')
                                    ->relationship('portfolios', 'title')
                                    ->multiple()
                                    ->searchable()
                                    ->preload(),
                                Select::make('faqs')
                                    ->label('FAQs')
                                    ->relationship('faqs', 'question')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Call to action')
                            ->disabled($locked)
                            ->collapsible()
                            ->collapsed()
                            ->columns(2)
                            ->schema([
                                TextInput::make('cta_title')->maxLength(150)->placeholder('Ready to get started?'),
                                Textarea::make('cta_text')->rows(2)->maxLength(400),
                            ]),

                        SeoSection::make('services.manage'),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        ContentFields::publishing(ContentStatus::class, 'services.manage'),

                        Section::make('Media')
                            ->disabled($locked)
                            ->schema([
                                MediaPicker::make('hero_media_id', 'heroMedia', 'Hero image'),
                                IllustrationSelect::make(),
                                TextInput::make('video_url')
                                    ->label('Video URL (YouTube or Vimeo)')
                                    ->url()
                                    ->maxLength(2048)
                                    ->regex('#^https://(www\.)?(youtube\.com/watch\?v=|youtu\.be/|vimeo\.com/)[\w\-?=&/]+$#')
                                    ->validationMessages(['regex' => 'Only YouTube or Vimeo links are supported.']),
                            ]),

                        Section::make('Record')
                            ->hiddenOn('create')
                            ->schema([
                                TextEntry::make('creator.name')->label('Created by')->placeholder('System'),
                                TextEntry::make('editor.name')->label('Last edited by')->placeholder('System'),
                                TextEntry::make('updated_at')->label('Last updated')->since(),
                            ]),
                    ]),
            ]);
    }
}
