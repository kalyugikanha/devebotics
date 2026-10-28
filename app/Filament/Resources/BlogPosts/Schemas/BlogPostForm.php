<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use App\Enums\PostStatus;
use App\Filament\Forms\ContentFields;
use App\Filament\Forms\MediaPicker;
use App\Filament\Forms\SeoSection;
use App\Models\BlogPost;
use App\Services\Blog\AiDraftingAssistant;
use App\Services\Blog\BlogWorkflow;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use RuntimeException;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        // Content is locked for users who may open the form but not edit content (e.g. SEO managers).
        $contentLocked = fn (?BlogPost $record): bool => $record !== null && Gate::denies('editContent', $record);

        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Reviewer feedback')
                            ->icon('heroicon-o-chat-bubble-left-ellipsis')
                            ->visible(fn (?BlogPost $record): bool => filled($record?->review_notes))
                            ->schema([
                                TextEntry::make('review_notes')->hiddenLabel()->prose(),
                                TextEntry::make('reviewer.name')->label('From')->placeholder('—'),
                            ]),

                        Section::make('Article')
                            ->disabled($contentLocked)
                            ->schema([
                                ContentFields::title(),
                                ContentFields::slug('blog_posts', '/blog'),
                                Textarea::make('excerpt')
                                    ->label('Summary')
                                    ->required()
                                    ->rows(2)
                                    ->maxLength(300)
                                    ->helperText('Shown on blog cards and used as the default meta description.'),
                                ContentFields::richEditor('body', 'Body', required: true)
                                    ->hintAction(self::aiOutlineAction()),
                                Hidden::make('is_ai_assisted'),
                            ]),

                        SeoSection::make('blogs.create'),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Publishing')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                ToggleButtons::make('status')
                                    ->options(PostStatus::class)
                                    ->default(PostStatus::Draft->value)
                                    ->required()
                                    ->inline()
                                    ->disableOptionWhen(fn (string $value, ?BlogPost $record): bool => $value !== $record?->status?->value
                                        && ! in_array(PostStatus::from($value), self::allowedStatuses(), true))
                                    // A non-publisher cannot change the status of a live/scheduled/archived post at all.
                                    ->disabled(fn (?BlogPost $record): bool => $record !== null && ! in_array($record->status, self::allowedStatuses(), true))
                                    ->helperText(fn (): ?string => Auth::user()?->can('blogs.publish') ? null : 'Publishing and scheduling are done by an editor with publishing rights.'),
                                DateTimePicker::make('published_at')
                                    ->label('Publish date')
                                    ->seconds(false)
                                    ->native(false)
                                    ->disabled(fn (): bool => ! Auth::user()?->can('blogs.publish'))
                                    ->required(fn (Get $get): bool => $get('status') === PostStatus::Scheduled->value)
                                    ->after(fn (Get $get): ?string => $get('status') === PostStatus::Scheduled->value ? 'now' : null)
                                    ->helperText('For scheduled posts choose a future date. Empty when published means "now".'),
                                Toggle::make('is_featured')
                                    ->label('Feature on the blog')
                                    ->disabled(fn (): bool => ! Auth::user()?->canAny(['blogs.publish', 'blogs.edit'])),
                                Toggle::make('allow_comments')->label('Allow comments')->default(true),
                                Toggle::make('is_demo')
                                    ->label('Demo content')
                                    ->helperText('Seeded demonstration content is labelled publicly. Untick once replaced with real content.')
                                    ->visible(fn (Get $get): bool => (bool) $get('is_demo')),
                            ]),

                        Section::make('Organisation')
                            ->disabled($contentLocked)
                            ->schema([
                                Select::make('author_id')
                                    ->label('Author')
                                    ->relationship('author', 'name', fn ($query) => $query->where('is_active', true))
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (): bool => (bool) Auth::user()?->canAny(['blogs.edit', 'blogs.publish']))
                                    ->helperText('Defaults to your own author profile.'),
                                Select::make('blog_category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name', fn ($query) => $query->where('is_active', true))
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([TextInput::make('name')->required()->maxLength(120)])
                                    ->createOptionAction(fn (Action $action) => $action->visible(fn (): bool => (bool) Auth::user()?->can('blog_taxonomies.manage'))),
                                Select::make('tags')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([TextInput::make('name')->required()->maxLength(60)])
                                    ->createOptionAction(fn (Action $action) => $action->visible(fn (): bool => (bool) Auth::user()?->can('blog_taxonomies.manage'))),
                            ]),

                        Section::make('Featured image')
                            ->disabled($contentLocked)
                            ->schema([
                                MediaPicker::make('featured_media_id', 'featuredMedia', 'Featured image'),
                            ]),

                        Section::make('Record')
                            ->hiddenOn('create')
                            ->schema([
                                TextEntry::make('creator.name')->label('Created by')->placeholder('System'),
                                TextEntry::make('reviewer.name')->label('Reviewed by')->placeholder('Not reviewed yet'),
                                TextEntry::make('reading_time_minutes')->label('Reading time')->suffix(' min'),
                                TextEntry::make('updated_at')->label('Last updated')->since(),
                            ]),
                    ]),
            ]);
    }

    /**
     * @return array<int, PostStatus>
     */
    public static function allowedStatuses(): array
    {
        $user = Auth::user();

        return $user ? app(BlogWorkflow::class)->allowedStatuses($user) : [];
    }

    /**
     * Optional AI outline (drafts only). Hidden unless an administrator has
     * enabled it and configured an API key on the server.
     */
    public static function aiOutlineAction(): Action
    {
        return Action::make('aiOutline')
            ->label('AI outline')
            ->icon('heroicon-o-sparkles')
            ->color('gray')
            ->visible(fn (?BlogPost $record): bool => app(AiDraftingAssistant::class)->isAvailable()
                && ($record === null ? (bool) Auth::user()?->can('blogs.create') : Gate::allows('editContent', $record)))
            ->modalHeading('Generate an outline with AI')
            ->modalDescription('The outline replaces the current body so you can edit it. Nothing is published — always review AI output and verify facts.')
            ->schema([
                TextInput::make('topic')
                    ->required()
                    ->maxLength(300)
                    ->default(fn (Get $get): ?string => $get('title')),
                Textarea::make('notes')->label('Notes for the AI (optional)')->rows(3)->maxLength(1500),
            ])
            ->modalSubmitActionLabel('Generate outline')
            ->action(function (array $data, Set $set): void {
                try {
                    $html = app(AiDraftingAssistant::class)->outline((string) $data['topic'], $data['notes'] ?? null);
                } catch (RuntimeException $exception) {
                    Notification::make()->title('AI outline failed')->body($exception->getMessage())->danger()->send();

                    return;
                }

                $set('body', $html);
                $set('is_ai_assisted', true);

                Notification::make()->title('Outline inserted')->body('Review and edit it before submitting the post.')->success()->send();
            });
    }
}
