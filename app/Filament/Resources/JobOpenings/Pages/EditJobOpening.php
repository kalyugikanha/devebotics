<?php

namespace App\Filament\Resources\JobOpenings\Pages;

use App\Filament\Resources\JobOpenings\JobOpeningResource;
use App\Filament\Tables\ContentTable;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditJobOpening extends EditRecord
{
    protected static string $resource = JobOpeningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContentTable::viewOnSite(),
            ContentTable::preview(),
            ActionGroup::make([DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make()]),
        ];
    }
}
