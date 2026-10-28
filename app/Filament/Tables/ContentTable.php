<?php

namespace App\Filament\Tables;

use App\Contracts\HasPublicUrl;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

/**
 * Shared table columns, filters and actions for publishable content.
 */
final class ContentTable
{
    public static function status(): TextColumn
    {
        return TextColumn::make('status')->badge()->sortable();
    }

    public static function featured(): IconColumn
    {
        return IconColumn::make('is_featured')->label('Featured')->boolean()->trueIcon('heroicon-s-star')->falseIcon('heroicon-o-star')->trueColor('warning')->sortable();
    }

    public static function publishedAt(): TextColumn
    {
        return TextColumn::make('published_at')->label('Published')->dateTime('M j, Y H:i')->sortable()->placeholder('—');
    }

    public static function updatedAt(): TextColumn
    {
        return TextColumn::make('updated_at')->label('Updated')->since()->sortable()->toggleable();
    }

    public static function demo(): IconColumn
    {
        return IconColumn::make('is_demo')->label('Demo')->boolean()->trueIcon('heroicon-o-beaker')->falseIcon('heroicon-o-check')->trueColor('warning')->falseColor('gray')->toggleable(isToggledHiddenByDefault: true);
    }

    /**
     * @param  class-string<BackedEnum>  $enum
     */
    public static function statusFilter(string $enum): SelectFilter
    {
        return SelectFilter::make('status')->options($enum)->multiple();
    }

    public static function featuredFilter(): TernaryFilter
    {
        return TernaryFilter::make('is_featured')->label('Featured');
    }

    public static function viewOnSite(): Action
    {
        return Action::make('viewOnSite')
            ->label('View')
            ->icon('heroicon-o-arrow-top-right-on-square')
            ->color('gray')
            ->url(fn (Model $record): ?string => $record instanceof HasPublicUrl ? $record->publicUrl() : null, shouldOpenInNewTab: true)
            ->visible(fn (Model $record): bool => $record instanceof HasPublicUrl && $record->isPubliclyVisible());
    }

    /**
     * Time-limited signed preview of unpublished content (also requires an authenticated admin).
     */
    public static function preview(): Action
    {
        return Action::make('preview')
            ->label('Preview')
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->url(fn (Model $record): string => URL::temporarySignedRoute('preview.show', now()->addHour(), [
                'type' => $record->getMorphClass(),
                'id' => $record->getKey(),
            ]), shouldOpenInNewTab: true)
            ->visible(fn (Model $record): bool => $record instanceof HasPublicUrl && ! $record->isPubliclyVisible() && ! (method_exists($record, 'trashed') && $record->trashed()));
    }

    /**
     * Bulk-set a status. Each record is authorised and saved individually so
     * observers (audit log, revisions, redirects) run.
     */
    public static function setStatusBulk(string $name, string $label, BackedEnum $status, string $permission, string $icon, string $color = 'gray'): BulkAction
    {
        return BulkAction::make($name)
            ->label($label)
            ->icon($icon)
            ->color($color)
            ->requiresConfirmation()
            ->authorize(fn (): bool => (bool) Auth::user()?->can($permission))
            ->action(function (Collection $records) use ($status, $permission): void {
                $updated = 0;

                foreach ($records as $record) {
                    if (Gate::allows('update', $record) && Gate::allows($permission)) {
                        $record->status = $status;
                        $record->save();
                        $updated++;
                    }
                }

                Notification::make()->title("{$updated} record(s) updated")->success()->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
