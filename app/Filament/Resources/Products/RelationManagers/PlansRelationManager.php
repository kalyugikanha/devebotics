<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Enums\BillingPeriod;
use App\Filament\Forms\LinkInput;
use App\Models\ProductPlan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PlansRelationManager extends RelationManager
{
    protected static string $relationship = 'plans';

    protected static ?string $title = 'Pricing plans';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                TextInput::make('name')->required()->maxLength(80)->columnSpan(3),
                TextInput::make('price')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(9999999999)
                    ->helperText('Leave empty to show "Contact us" instead of a price.'),
                Select::make('currency')
                    ->options(['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP', 'INR' => 'INR', 'AED' => 'AED', 'AUD' => 'AUD', 'CAD' => 'CAD', 'SGD' => 'SGD'])
                    ->default('USD')
                    ->required(),
                Select::make('billing_period')->options(BillingPeriod::class)->default(BillingPeriod::Monthly->value)->required(),
                TextInput::make('description')->maxLength(250)->columnSpan(3),
                TagsInput::make('features')->placeholder('Add a feature and press Enter')->columnSpan(3),
                TextInput::make('cta_label')->label('Button label')->maxLength(40)->placeholder('Get started'),
                LinkInput::make('cta_url', 'Button link')->columnSpan(2),
                Toggle::make('is_highlighted')->label('Highlight as recommended'),
                Toggle::make('is_active')->label('Active')->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('name')->weight('medium')->searchable(),
                TextColumn::make('price')
                    ->state(fn (ProductPlan $record): string => $record->formattedPrice() ?? 'Contact us')
                    ->description(fn (ProductPlan $record): ?string => $record->billing_period?->getLabel()),
                IconColumn::make('is_highlighted')->label('Recommended')->boolean(),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No pricing plans')
            ->emptyStateDescription('Plans are only displayed when the product uses the "Published pricing plans" model.');
    }
}
