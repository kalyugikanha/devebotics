<?php

namespace App\Filament\Resources\Teams\Tables;

use App\Enums\InquiryStatus;
use App\Enums\InquiryTopic;
use App\Models\Team;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TeamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withCount(['members', 'inquiries as open_inquiries_count' => fn (Builder $q) => $q->whereIn('status', InquiryStatus::values(InquiryStatus::open()))->where('is_spam', false)]))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ColorColumn::make('color')->label('')->default('#94a3b8'),
                TextColumn::make('name')->searchable()->weight('medium')->description(fn (Team $record): ?string => $record->email),
                TextColumn::make('topics')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => InquiryTopic::tryFrom($state)?->getLabel() ?? $state)
                    ->placeholder('No topics'),
                TextColumn::make('members_count')->label('Members')->numeric(),
                TextColumn::make('open_inquiries_count')->label('Open inquiries')->numeric()->badge()->color('info'),
                IconColumn::make('auto_assign')->label('Auto-assign')->boolean(),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->modalDescription('Inquiries of this team become unrouted; nothing else is deleted.'),
            ])
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateHeading('No teams yet')
            ->emptyStateDescription('Create teams (sales, support, HR…) so inquiries reach the right people automatically.')
            ->emptyStateActions([CreateAction::make()]);
    }
}
