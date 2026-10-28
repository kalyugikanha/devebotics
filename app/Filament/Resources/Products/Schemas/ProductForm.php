<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ContentStatus;
use App\Enums\PricingModel;
use App\Enums\ProductAvailability;
use App\Filament\Forms\ContentFields;
use App\Filament\Forms\IllustrationSelect;
use App\Filament\Forms\MediaPicker;
use App\Filament\Forms\SeoSection;
use App\Models\Product;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        $locked = ContentFields::lockedUnless('products.manage');

        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Product details')
                            ->disabled($locked)
                            ->columns(2)
                            ->schema([
                                ContentFields::title('name', 'Product name')->columnSpanFull(),
                                ContentFields::slug('products', '/products'),
                                TextInput::make('tagline')->maxLength(180)->helperText('One-line value proposition.'),
                                Select::make('product_category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([TextInput::make('name')->required()->maxLength(120)]),
                                Textarea::make('target_audience')->label('Target audience')->rows(2)->maxLength(600),
                                Textarea::make('summary')
                                    ->label('Card summary')
                                    ->rows(2)
                                    ->maxLength(300)
                                    ->required()
                                    ->helperText('Shown on cards and used as the default meta description.')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Detailed description')
                            ->disabled($locked)
                            ->collapsible()
                            ->schema([ContentFields::richEditor('description', 'Description')]),

                        Section::make('Features & benefits')
                            ->disabled($locked)
                            ->collapsible()
                            ->schema([
                                Tabs::make('Lists')->tabs([
                                    Tab::make('Features')->icon('heroicon-o-squares-2x2')->schema([ContentFields::listItems('features', 'Features', 16)]),
                                    Tab::make('Benefits')->icon('heroicon-o-check-circle')->schema([ContentFields::listItems('benefits', 'Benefits')]),
                                ]),
                            ]),

                        Section::make('Pricing & availability')
                            ->description('Only real, approved information. Published prices appear only when the pricing model is "Published pricing plans" and active plans exist (Plans tab below).')
                            ->disabled($locked)
                            ->collapsible()
                            ->columns(2)
                            ->schema([
                                Select::make('pricing_model')->options(PricingModel::class)->default(PricingModel::ContactSales->value)->required()->live(),
                                Select::make('availability')->options(ProductAvailability::class)->default(ProductAvailability::Available->value)->required(),
                                TextInput::make('pricing_note')->label('Pricing note')->maxLength(200)->columnSpanFull()->placeholder('Pricing depends on users and modules.'),
                                Toggle::make('has_demo')->label('Offer a demo request')->default(true),
                                Toggle::make('has_free_trial')->label('Free trial available')->live(),
                                TextInput::make('trial_days')
                                    ->label('Trial length (days)')
                                    ->numeric()
                                    ->integer()
                                    ->minValue(1)
                                    ->maxValue(365)
                                    ->visible(fn (Get $get): bool => (bool) $get('has_free_trial')),
                            ]),

                        Section::make('Links')
                            ->disabled($locked)
                            ->collapsible()
                            ->collapsed()
                            ->columns(3)
                            ->schema([
                                TextInput::make('website_url')->label('Product website')->url()->maxLength(2048),
                                TextInput::make('documentation_url')->label('Documentation')->url()->maxLength(2048),
                                TextInput::make('support_url')->label('Support')->url()->maxLength(2048),
                            ]),

                        Section::make('Relationships')
                            ->disabled($locked)
                            ->collapsible()
                            ->columns(2)
                            ->schema([
                                Select::make('technologies')->relationship('technologies', 'name')->multiple()->searchable()->preload()
                                    ->createOptionForm([TextInput::make('name')->required()->maxLength(80)]),
                                Select::make('industries')->label('Industries served')->relationship('industries', 'name')->multiple()->searchable()->preload(),
                                Select::make('services')->label('Related services')->relationship('services', 'title')->multiple()->searchable()->preload(),
                                Select::make('relatedProducts')
                                    ->label('Related products')
                                    ->relationship('relatedProducts', 'name', fn (Builder $query, ?Product $record) => $query->when($record, fn (Builder $q) => $q->whereKeyNot($record->getKey())))
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->maxItems(6),
                                Select::make('portfolios')->label('Case studies')->relationship('portfolios', 'title')->multiple()->searchable()->preload(),
                                Select::make('faqs')->label('FAQs')->relationship('faqs', 'question')->multiple()->searchable()->preload(),
                            ]),

                        Section::make('Screenshot gallery')
                            ->disabled($locked)
                            ->collapsible()
                            ->schema([
                                Repeater::make('galleryItems')
                                    ->hiddenLabel()
                                    ->relationship()
                                    ->orderColumn('sort_order')
                                    ->schema([
                                        MediaPicker::make('media_id', 'media', 'Image')->required(),
                                        TextInput::make('caption')->maxLength(200),
                                    ])
                                    ->columns(2)
                                    ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => [...$data, 'collection' => 'gallery'])
                                    ->defaultItems(0)
                                    ->maxItems(20)
                                    ->collapsible()
                                    ->addActionLabel('Add screenshot'),
                            ]),

                        SeoSection::make('products.manage'),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        ContentFields::publishing(ContentStatus::class, 'products.manage'),

                        Section::make('Media')
                            ->disabled($locked)
                            ->schema([
                                MediaPicker::make('logo_media_id', 'logo', 'Product logo'),
                                MediaPicker::make('hero_media_id', 'heroMedia', 'Hero image'),
                                IllustrationSelect::make(),
                                TextInput::make('demo_video_url')
                                    ->label('Demo video (YouTube or Vimeo)')
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
