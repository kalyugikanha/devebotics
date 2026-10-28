<?php

namespace App\Filament\Resources\Testimonials\Tables;

use App\Enums\TestimonialStatus;
use App\Filament\Resources\Testimonials\Actions\TestimonialActions;
use App\Filament\Tables\ContentTable;
use App\Models\Testimonial;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('service'))
            ->defaultSort('created_at', 'desc')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable(['client_name', 'company_name'])
                    ->weight('medium')
                    ->description(fn (Testimonial $record): ?string => collect([$record->company_name, $record->service?->title])->filter()->implode(' · ')),
                TextColumn::make('rating')
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state).str_repeat('☆', 5 - $state))
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('feedback')->limit(80)->wrap()->toggleable(),
                TextColumn::make('status')->badge()->sortable(),
                IconColumn::make('publish_permission')->label('Publish OK')->boolean()->tooltip('Client permission to publish'),
                IconColumn::make('display_identity_consent')->label('Identity')->boolean()->tooltip('Consent to show name, company and photo'),
                ContentTable::featured(),
                TextColumn::make('source')
                    ->formatStateUsing(fn (string $state): string => $state === 'admin' ? 'Staff' : 'Form')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Received')->since()->sortable(),
                ContentTable::demo(),
            ])
            ->filters([
                SelectFilter::make('status')->options(TestimonialStatus::class),
                SelectFilter::make('rating')->options([5 => '5 stars', 4 => '4 stars', 3 => '3 stars', 2 => '2 stars', 1 => '1 star']),
                TernaryFilter::make('publish_permission')->label('Permission to publish'),
                ContentTable::featuredFilter(),
                SelectFilter::make('source')->options(['client_form' => 'Client feedback form', 'admin' => 'Entered by staff']),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ActionGroup::make([
                    ...TestimonialActions::all(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right')
            ->emptyStateHeading('No feedback yet')
            ->emptyStateDescription('Feedback submitted on the website waits here for moderation.');
    }
}
