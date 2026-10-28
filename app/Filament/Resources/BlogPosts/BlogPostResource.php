<?php

namespace App\Filament\Resources\BlogPosts;

use App\Enums\PostStatus;
use App\Filament\AdminNavigationGroup;
use App\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPosts\Pages\EditBlogPost;
use App\Filament\Resources\BlogPosts\Pages\ListBlogPosts;
use App\Filament\Resources\BlogPosts\Pages\ViewBlogPost;
use App\Filament\Resources\BlogPosts\Schemas\BlogPostForm;
use App\Filament\Resources\BlogPosts\Schemas\BlogPostInfolist;
use App\Filament\Resources\BlogPosts\Tables\BlogPostsTable;
use App\Models\BlogPost;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Blog;

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Posts';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return BlogPostForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BlogPostInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlogPostsTable::configure($table);
    }

    /**
     * Authors without `blogs.view` (or an editorial permission) only ever see
     * their own posts — this also scopes record route binding (no IDOR).
     */
    public static function getEloquentQuery(): Builder
    {
        return static::scopeToVisiblePosts(parent::getEloquentQuery());
    }

    public static function scopeToVisiblePosts(Builder $query): Builder
    {
        $user = Auth::user();

        if (! $user instanceof User || static::canSeeAllPosts($user)) {
            return $query;
        }

        $authorId = $user->author()->value('id');

        return $query->where(fn (Builder $q) => $q
            ->where('blog_posts.created_by', $user->getKey())
            ->when($authorId, fn (Builder $q) => $q->orWhere('blog_posts.author_id', $authorId)));
    }

    public static function canSeeAllPosts(User $user): bool
    {
        return $user->canAny(['blogs.view', 'blogs.edit', 'blogs.review', 'blogs.publish', 'seo.manage']);
    }

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'excerpt'];
    }

    public static function getNavigationBadge(): ?string
    {
        if (! Auth::user()?->can('blogs.review')) {
            return null;
        }

        $count = BlogPost::query()->where('status', PostStatus::InReview->value)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Posts awaiting review';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogPosts::route('/'),
            'create' => CreateBlogPost::route('/create'),
            'view' => ViewBlogPost::route('/{record}'),
            'edit' => EditBlogPost::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
