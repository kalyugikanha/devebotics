<?php

namespace App\Filament\Resources\JobOpenings\Schemas;

use App\Enums\ContentStatus;
use App\Enums\EmploymentType;
use App\Enums\WorkplaceType;
use App\Filament\Forms\ContentFields;
use App\Filament\Forms\SeoSection;
use App\Models\JobOpening;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class JobOpeningForm
{
    public static function configure(Schema $schema): Schema
    {
        $locked = ContentFields::lockedUnless('careers.manage');

        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Role')
                            ->disabled($locked)
                            ->columns(2)
                            ->schema([
                                ContentFields::title('title', 'Job title')->columnSpanFull(),
                                ContentFields::slug('job_openings', '/careers'),
                                TextInput::make('department')
                                    ->maxLength(120)
                                    ->datalist(fn (): array => JobOpening::query()->whereNotNull('department')->distinct()->orderBy('department')->pluck('department')->all()),
                                TextInput::make('location')->maxLength(120)->placeholder('e.g. Remote, Bengaluru, London'),
                                TextInput::make('country_code')
                                    ->label('Country code')
                                    ->maxLength(2)
                                    ->rule('alpha')
                                    ->dehydrateStateUsing(fn (?string $state): ?string => $state ? strtoupper($state) : null)
                                    ->helperText('ISO 3166 alpha-2 (e.g. IN, GB, US). Used in job posting structured data.'),
                                Select::make('employment_type')->options(EmploymentType::class)->required()->default(EmploymentType::FullTime->value),
                                ToggleButtons::make('workplace_type')->options(WorkplaceType::class)->inline()->required()->default(WorkplaceType::OnSite->value),
                                TextInput::make('experience_level')->maxLength(80)->placeholder('e.g. Senior, 5+ years'),
                                Textarea::make('summary')
                                    ->required()
                                    ->rows(2)
                                    ->maxLength(400)
                                    ->helperText('Shown on the careers listing and used as the default meta description.')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Description')
                            ->disabled($locked)
                            ->collapsible()
                            ->schema([ContentFields::richEditor('description', 'Full description')]),

                        Section::make('Details')
                            ->disabled($locked)
                            ->collapsible()
                            ->schema([
                                Tabs::make('Lists')->tabs([
                                    Tab::make('Responsibilities')->schema([ContentFields::listItems('responsibilities', 'Responsibilities', 15, withIcon: false)]),
                                    Tab::make('Requirements')->schema([ContentFields::listItems('requirements', 'Requirements', 15, withIcon: false)]),
                                    Tab::make('Benefits')->schema([ContentFields::listItems('benefits', 'Benefits', 15, withIcon: false)]),
                                ]),
                            ]),

                        Section::make('Salary')
                            ->disabled($locked)
                            ->collapsible()
                            ->schema([
                                Toggle::make('show_salary')->label('Show salary range publicly')->live(),
                                Grid::make(4)->schema([
                                    TextInput::make('salary_min')->label('Minimum')->numeric()->minValue(0)
                                        ->required(fn (Get $get): bool => (bool) $get('show_salary')),
                                    TextInput::make('salary_max')->label('Maximum')->numeric()->minValue(0)
                                        ->gte('salary_min'),
                                    TextInput::make('salary_currency')->label('Currency')->maxLength(3)->placeholder('USD')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => $state ? strtoupper($state) : null)
                                        ->required(fn (Get $get): bool => (bool) $get('show_salary')),
                                    Select::make('salary_period')->label('Per')->options(['hour' => 'Hour', 'month' => 'Month', 'year' => 'Year'])->default('year'),
                                ]),
                            ]),

                        SeoSection::make('careers.manage'),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        ContentFields::publishing(ContentStatus::class, 'careers.manage'),
                        Section::make('Closing date')
                            ->disabled($locked)
                            ->schema([
                                DatePicker::make('valid_through')
                                    ->label('Applications close on')
                                    ->native(false)
                                    ->helperText('After this date the role stops accepting applications and is removed from job search structured data.'),
                            ]),
                        Section::make('Record')
                            ->hiddenOn('create')
                            ->schema([
                                TextEntry::make('applications_count')->label('Applications')->state(fn (?JobOpening $record): int => $record?->applications()->count() ?? 0),
                                TextEntry::make('creator.name')->label('Created by')->placeholder('System'),
                                TextEntry::make('updated_at')->label('Last updated')->since(),
                            ]),
                    ]),
            ]);
    }
}
