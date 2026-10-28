<?php

namespace App\Filament\Resources\Teams\Schemas;

use App\Enums\InquiryTopic;
use App\Enums\TeamRole;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Team')
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')->required()->maxLength(120),
                                TextInput::make('slug')->maxLength(140)->rule('alpha_dash')->unique('teams', 'slug', ignoreRecord: true)
                                    ->helperText('Leave empty to generate from the name.'),
                                Textarea::make('description')->rows(2)->maxLength(500)->columnSpanFull(),
                                TextInput::make('email')->label('Shared mailbox (optional)')->email()->maxLength(190)
                                    ->helperText('Also receives an alert for every inquiry routed to this team.'),
                                ColorPicker::make('color')->label('Colour')->regex('/^#[0-9a-fA-F]{6}$/'),
                            ]),
                        Section::make('Members')
                            ->description('Members see this team’s queue and can pick up inquiries. Team leads can also assign inquiries to members.')
                            ->schema([
                                Repeater::make('memberships')
                                    ->relationship()
                                    ->hiddenLabel()
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addActionLabel('Add member')
                                    ->schema([
                                        Select::make('user_id')
                                            ->label('User')
                                            ->options(fn (): array => self::eligibleUsers())
                                            ->in(fn (): array => array_keys(self::eligibleUsers()))
                                            ->searchable()
                                            ->required()
                                            ->distinct()
                                            ->helperText('Active users whose role allows working on inquiries.'),
                                        Select::make('role')
                                            ->options(TeamRole::class)
                                            ->default(TeamRole::Member->value)
                                            ->required(),
                                    ]),
                            ]),
                    ]),
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Routing')
                            ->description('New inquiries go to the first active team (in list order) that handles their topic; a team that also handles the selected service wins.')
                            ->schema([
                                CheckboxList::make('topics')
                                    ->label('Topics this team handles')
                                    ->options(InquiryTopic::class),
                                Select::make('services')
                                    ->label('Services this team handles')
                                    ->relationship('services', 'title')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                                Toggle::make('auto_assign')
                                    ->label('Auto-assign (round-robin)')
                                    ->helperText('Assign new inquiries to the member with the fewest open inquiries.'),
                                Toggle::make('is_active')->label('Active')->default(true)
                                    ->helperText('Inactive teams receive no new inquiries.'),
                            ]),
                    ]),
            ]);
    }

    /**
     * @return array<int, string>
     */
    public static function eligibleUsers(): array
    {
        return User::query()->active()->orderBy('name')->get()
            ->filter(fn (User $user): bool => $user->canAny(['inquiries.view', 'inquiries.view_team', 'inquiries.view_all']))
            ->mapWithKeys(fn (User $user): array => [$user->getKey() => $user->name.' ('.$user->email.')'])
            ->all();
    }
}
