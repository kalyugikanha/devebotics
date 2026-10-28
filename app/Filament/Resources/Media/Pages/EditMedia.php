<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\Actions\MediaDeletion;
use App\Filament\Resources\Media\Actions\ReplaceMediaFileAction;
use App\Filament\Resources\Media\MediaResource;
use App\Models\Media;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditMedia extends EditRecord
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open')
                ->label('Open file')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn (Media $record): string => $record->url(), shouldOpenInNewTab: true),
            ReplaceMediaFileAction::make(),
            MediaDeletion::action(),
        ];
    }
}
