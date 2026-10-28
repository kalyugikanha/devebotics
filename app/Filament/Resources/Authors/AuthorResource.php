<?php

namespace App\Filament\Resources\Authors;

use App\Filament\AdminNavigationGroup;
use App\Filament\Forms\MediaPicker;
use App\Filament\Resources\Authors\Pages\ManageAuthors;
use App\Models\Author;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Blog;

    protected static ?int $navigationSort = 40;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                TextInput::make('name')->required()->maxLength(120),
                TextInput::make('job_title')->maxLength(120),
                TextInput::make('slug')
                    ->maxLength(180)
                    ->rule('alpha_dash')
                    ->unique('authors', 'slug', ignoreRecord: true)
                    ->helperText('Public profile URL: /blog/author/{slug}.'),
                Select::make('user_id')
                    ->label('Linked admin user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->unique('authors', 'user_id', ignoreRecord: true)
                    ->helperText('Optional. Lets this person write posts under this author profile.'),
            ]),
            Textarea::make('bio')->rows(3)->maxLength(1000)->columnSpanFull(),
            MediaPicker::make('avatar_media_id', 'avatar', 'Avatar'),
            Grid::make(3)->schema([
                TextInput::make('website_url')->label('Website')->url()->maxLength(2048),
                TextInput::make('linkedin_url')->label('LinkedIn')->url()->maxLength(2048),
                TextInput::make('x_url')->label('X / Twitter')->url()->maxLength(2048),
            ]),
            Toggle::make('is_active')->label('Active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['avatar', 'user'])->withCount('posts'))
            ->defaultSort('name')
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('')
                    ->state(fn (Author $record): ?string => $record->avatar?->variantUrl(480) ?? $record->avatar?->url())
                    ->circular()
                    ->checkFileExistence(false),
                TextColumn::make('name')->searchable()->sortable()->weight('medium')->description(fn (Author $record): ?string => $record->job_title),
                TextColumn::make('user.email')->label('Linked user')->placeholder('—')->toggleable(),
                TextColumn::make('posts_count')->label('Posts')->sortable(),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->modalDescription('Posts by this author will no longer show an author. This cannot be undone.'),
            ])
            ->emptyStateHeading('No authors yet')
            ->emptyStateActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAuthors::route('/'),
        ];
    }
}
