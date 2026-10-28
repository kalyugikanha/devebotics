<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Resources\BlogPosts\Actions\BlogWorkflowActions;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Filament\Tables\ContentTable;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBlogPost extends ViewRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContentTable::viewOnSite(),
            ContentTable::preview(),
            ActionGroup::make(BlogWorkflowActions::all())->label('Workflow')->icon('heroicon-m-arrow-path')->button()->color('gray'),
            EditAction::make(),
        ];
    }
}
