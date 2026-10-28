<?php

namespace App\Filament\Resources\JobApplications\Tables;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\JobApplications\Actions\JobApplicationActions;
use App\Models\JobApplication;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JobApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('jobOpening'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')->searchable()->fontFamily('mono')->size('sm')->copyable(),
                TextColumn::make('name')
                    ->label('Candidate')
                    ->searchable(['name', 'email'])
                    ->weight('medium')
                    ->description(fn (JobApplication $record): string => $record->email),
                TextColumn::make('jobOpening.title')->label('Position')->placeholder('Open application')->limit(35),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('rating')
                    ->formatStateUsing(fn (?int $state): string => $state ? str_repeat('★', $state) : '—')
                    ->color('warning')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('created_at')->label('Applied')->since()->sortable()->dateTimeTooltip(),
            ])
            ->filters([
                SelectFilter::make('job_opening_id')->label('Position')->relationship('jobOpening', 'title')->searchable()->preload(),
                SelectFilter::make('status')->options(ApplicationStatus::class)->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                ActionGroup::make([
                    JobApplicationActions::changeStatus(),
                    JobApplicationActions::rate(),
                    JobApplicationActions::downloadResume(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateHeading('No applications yet')
            ->emptyStateDescription('Applications submitted on the careers page appear here.');
    }
}
