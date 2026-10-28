<?php

namespace App\Filament\Resources\TeamMembers\Tables;

use App\Filament\Tables\ContentTable;
use App\Models\TeamMember;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TeamMembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('photo'))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('photo_preview')
                    ->label('')
                    ->state(fn (TeamMember $record): ?string => $record->photo ? ($record->photo->variantUrl(480) ?? $record->photo->url()) : null)
                    ->defaultImageUrl(fn (TeamMember $record): string => 'data:image/svg+xml;utf8,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"><rect width="40" height="40" rx="20" fill="#ede9fe"/><text x="20" y="25" text-anchor="middle" font-family="Arial" font-size="14" font-weight="700" fill="#6d28d9">'.e($record->initials()).'</text></svg>'))
                    ->circular()
                    ->imageSize(36)
                    ->checkFileExistence(false),
                TextColumn::make('name')->searchable()->sortable()->weight('medium')->description(fn (TeamMember $record): string => $record->job_title),
                TextColumn::make('department')->badge()->color('gray')->sortable(),
                IconColumn::make('is_leadership')->label('Leadership')->boolean(),
                ToggleColumn::make('is_active')->label('Visible'),
                ContentTable::demo(),
            ])
            ->filters([
                TernaryFilter::make('is_leadership')->label('Leadership'),
                TernaryFilter::make('is_active')->label('Visible'),
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
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateHeading('No team members yet')
            ->emptyStateDescription('Team members appear on the Team page and About page leadership section.')
            ->emptyStateActions([CreateAction::make()]);
    }
}
