<?php

namespace App\Filament\Resources\Portfolios\Pages;

use App\Filament\Actions\RevisionsAction;
use App\Filament\Resources\Portfolios\PortfolioResource;
use App\Filament\Tables\ContentTable;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPortfolio extends EditRecord
{
    protected static string $resource = PortfolioResource::class;

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
