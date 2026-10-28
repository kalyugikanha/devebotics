<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Actions\RevisionsAction;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Tables\ContentTable;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContentTable::viewOnSite(),
            ContentTable::preview(),
            RevisionsAction::make(),
            ActionGroup::make([
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ]),
        ];
    }
}
