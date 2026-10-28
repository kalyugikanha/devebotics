<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Enums\PostStatus;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Models\BlogPost;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBlogPosts extends ListRecords
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New post'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = ['all' => Tab::make('All')];

        foreach ([PostStatus::Draft, PostStatus::InReview, PostStatus::Scheduled, PostStatus::Published, PostStatus::Archived] as $status) {
            $tabs[$status->value] = Tab::make($status->getLabel())
                ->icon($status->getIcon())
                ->badge(fn (): int => BlogPostResource::scopeToVisiblePosts(BlogPost::query())->where('status', $status->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status->value));
        }

        return $tabs;
    }
}
