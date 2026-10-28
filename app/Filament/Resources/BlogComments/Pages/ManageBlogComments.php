<?php

namespace App\Filament\Resources\BlogComments\Pages;

use App\Filament\Resources\BlogComments\BlogCommentResource;
use Filament\Resources\Pages\ManageRecords;

class ManageBlogComments extends ManageRecords
{
    protected static string $resource = BlogCommentResource::class;
}
