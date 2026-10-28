<?php

namespace App\Filament\Resources\JobApplications\Actions;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

final class JobApplicationActions
{
    public static function changeStatus(): Action
    {
        return Action::make('changeStatus')
            ->label('Change status')
            ->icon('heroicon-o-arrow-path')
            ->authorize(fn (JobApplication $record): bool => Gate::allows('update', $record))
            ->schema([Select::make('status')->options(ApplicationStatus::class)->required()])
            ->fillForm(fn (JobApplication $record): array => ['status' => $record->status])
            ->action(function (JobApplication $record, array $data): void {
                $record->forceFill(['status' => $data['status'], 'reviewed_by' => Auth::id()])->save();
                Notification::make()->title('Status updated')->success()->send();
            });
    }

    public static function rate(): Action
    {
        return Action::make('rate')
            ->label('Rate')
            ->icon('heroicon-o-star')
            ->color('warning')
            ->authorize(fn (JobApplication $record): bool => Gate::allows('update', $record))
            ->schema([
                Select::make('rating')->options([5 => '★★★★★ Excellent', 4 => '★★★★ Strong', 3 => '★★★ Good', 2 => '★★ Weak', 1 => '★ Poor'])->placeholder('No rating'),
            ])
            ->fillForm(fn (JobApplication $record): array => ['rating' => $record->rating])
            ->action(function (JobApplication $record, array $data): void {
                $record->forceFill(['rating' => $data['rating'] ?: null, 'reviewed_by' => Auth::id()])->save();
                Notification::make()->title('Rating saved')->success()->send();
            });
    }

    public static function notes(): Action
    {
        return Action::make('notes')
            ->label('Internal notes')
            ->icon('heroicon-o-pencil-square')
            ->color('gray')
            ->authorize(fn (JobApplication $record): bool => Gate::allows('update', $record))
            ->modalDescription('Visible to the hiring team only.')
            ->schema([Textarea::make('internal_notes')->rows(8)->maxLength(10000)])
            ->fillForm(fn (JobApplication $record): array => ['internal_notes' => $record->internal_notes])
            ->action(function (JobApplication $record, array $data): void {
                $record->forceFill(['internal_notes' => $data['internal_notes'], 'reviewed_by' => Auth::id()])->save();
                Notification::make()->title('Notes saved')->success()->send();
            });
    }

    public static function downloadResume(): Action
    {
        return Action::make('downloadResume')
            ->label('Download résumé')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->url(fn (JobApplication $record): string => route('files.resume', $record), shouldOpenInNewTab: true)
            ->authorize(fn (JobApplication $record): bool => Gate::allows('downloadResume', $record))
            ->visible(fn (JobApplication $record): bool => filled($record->resume_path));
    }

    public static function emailCandidate(): Action
    {
        return Action::make('emailCandidate')
            ->label('Email candidate')
            ->icon('heroicon-o-envelope')
            ->color('gray')
            ->url(fn (JobApplication $record): string => 'mailto:'.rawurlencode($record->email).'?subject='.rawurlencode('Your application '.$record->reference));
    }
}
