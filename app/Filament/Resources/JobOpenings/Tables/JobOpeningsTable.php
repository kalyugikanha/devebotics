<?php

namespace App\Filament\Resources\JobOpenings\Tables;

use App\Enums\ContentStatus;
use App\Enums\EmploymentType;
use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Filament\Tables\ContentTable;
use App\Models\JobOpening;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class JobOpeningsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('applications'))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (JobOpening $record): string => collect([$record->department, $record->location])->filter()->implode(' · ')),
                TextColumn::make('employment_type')->label('Type')->badge()->color('gray')->toggleable(),
                TextColumn::make('workplace_type')->label('Workplace')->badge()->color('gray')->toggleable(),
                ContentTable::status(),
                TextColumn::make('valid_through')
                    ->label('Closes')
                    ->date('M j, Y')
                    ->sortable()
                    ->placeholder('No closing date')
                    ->color(fn (JobOpening $record): ?string => $record->valid_through?->isPast() ? 'danger' : null),
                TextColumn::make('applications_count')->label('Applications')->sortable()->badge()->color('info'),
                ContentTable::publishedAt(),
                ContentTable::demo(),
            ])
            ->filters([
                ContentTable::statusFilter(ContentStatus::class),
                SelectFilter::make('department')->options(fn (): array => JobOpening::query()->whereNotNull('department')->distinct()->orderBy('department')->pluck('department', 'department')->all()),
                SelectFilter::make('employment_type')->options(EmploymentType::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ContentTable::viewOnSite(),
                ContentTable::preview(),
                EditAction::make(),
                ActionGroup::make([
                    Action::make('applications')
                        ->label('View applications')
                        ->icon('heroicon-o-users')
                        ->url(fn (JobOpening $record): string => JobApplicationResource::getUrl('index', ['filters' => ['job_opening_id' => ['value' => $record->getKey()]]]))
                        ->visible(fn (): bool => (bool) Auth::user()?->can('applications.view')),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ContentTable::setStatusBulk('publish', 'Publish', ContentStatus::Published, 'careers.manage', 'heroicon-o-check-circle', 'success'),
                    ContentTable::setStatusBulk('unpublish', 'Move to draft', ContentStatus::Draft, 'careers.manage', 'heroicon-o-pencil-square'),
                    ContentTable::setStatusBulk('archive', 'Archive', ContentStatus::Archived, 'careers.manage', 'heroicon-o-archive-box', 'warning'),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-briefcase')
            ->emptyStateHeading('No job openings')
            ->emptyStateDescription('Published openings appear on the careers page until their closing date.')
            ->emptyStateActions([CreateAction::make()]);
    }
}
