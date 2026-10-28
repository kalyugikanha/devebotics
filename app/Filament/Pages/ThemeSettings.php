<?php

namespace App\Filament\Pages;

use App\Enums\ThemeMode;
use App\Filament\AdminNavigationGroup;
use App\Support\Audit\AuditLogger;
use App\Support\Settings\Settings;
use App\Support\Settings\SettingsRegistry;
use App\Support\Theme\ThemeManager;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use UnitEnum;

/**
 * Edits the DRAFT theme. Changes reach visitors only after "Publish";
 * administrators can preview the draft on the live site first.
 *
 * @property-read Schema $form
 */
class ThemeSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Appearance;

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Theme & colours';

    protected static ?string $title = 'Theme & colours';

    protected static ?string $slug = 'theme-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return (bool) Auth::user()?->can('appearance.manage');
    }

    public function mount(): void
    {
        $this->form->fill(app(ThemeManager::class)->draft());
    }

    public function getSubheading(): ?string
    {
        return $this->hasUnpublishedChanges()
            ? 'The draft differs from the published theme. Preview it, then publish when ready.'
            : 'The draft matches the published theme.';
    }

    public function form(Schema $schema): Schema
    {
        $palette = fn (string $scheme): array => collect(ThemeManager::COLOR_KEYS)
            ->map(fn (string $key) => ColorPicker::make($scheme.'.'.$key)
                ->label(Str::headline($key))
                ->hex()
                ->required()
                ->regex('/^#[0-9a-fA-F]{6}$/')
                ->live(onBlur: true)
                ->helperText(self::colorHelp($key)))
            ->all();

        return $schema
            ->statePath('data')
            ->components([
                Callout::make('Contrast check')
                    ->description(fn (Get $get): HtmlString => $this->contrastSummary($get))
                    ->color(fn (Get $get): string => $this->warningsFor($get) === [] ? 'success' : 'warning')
                    ->icon(fn (Get $get): string => $this->warningsFor($get) === [] ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle'),
                Section::make('Behaviour')->columns(3)->schema([
                    ToggleButtons::make('default_mode')->label('Default colour mode')->options(ThemeMode::class)->inline()->required(),
                    Toggle::make('allow_switcher')->label('Let visitors switch light / dark mode'),
                    Select::make('radius')
                        ->label('Corner style')
                        ->options(['sharp' => 'Sharp', 'soft' => 'Soft', 'rounded' => 'Rounded'])
                        ->in(array_keys(ThemeManager::RADIUS_PRESETS))
                        ->required()
                        ->selectablePlaceholder(false),
                ]),
                Grid::make(['lg' => 2])->schema([
                    Section::make('Light mode palette')->icon('heroicon-o-sun')->columns(2)->schema($palette('light')),
                    Section::make('Dark mode palette')->icon('heroicon-o-moon')->columns(2)->schema($palette('dark')),
                ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('saveDraft')
                ->footer([
                    Actions::make([
                        Action::make('saveDraft')->label('Save draft')->submit('saveDraft')->keyBindings(['mod+s']),
                    ])->sticky()->key('form-actions'),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Preview on website')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->action(function (): void {
                    $this->persistDraft();
                    $this->js('window.open('.json_encode(route('theme-preview.start')).', "_blank", "noopener")');
                }),
            Action::make('publish')
                ->label('Publish theme')
                ->icon('heroicon-o-rocket-launch')
                ->requiresConfirmation()
                ->modalDescription('Visitors will see the draft theme immediately.')
                ->action(fn () => $this->publish()),
            Action::make('resetDraft')
                ->label('Discard draft changes')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function (): void {
                    $published = app(ThemeManager::class)->published();
                    app(Settings::class)->set('theme', 'draft', $published);
                    $this->form->fill($published);
                    Notification::make()->title('Draft reset to the published theme')->success()->send();
                }),
            Action::make('restoreDefaults')
                ->label('Restore brand defaults')
                ->icon('heroicon-o-sparkles')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Replaces the draft with the original Softec Ai Solution palette. Nothing is published until you click Publish.')
                ->action(function (): void {
                    $defaults = SettingsRegistry::defaultTheme();
                    app(Settings::class)->set('theme', 'draft', $defaults);
                    $this->form->fill($defaults);
                    Notification::make()->title('Brand defaults restored to the draft')->success()->send();
                }),
        ];
    }

    public function saveDraft(): void
    {
        $this->persistDraft();
        Notification::make()->title('Draft saved')->body('Preview it on the website, then publish when ready.')->success()->send();
    }

    public function publish(): void
    {
        abort_unless(static::canAccess(), 403);

        $draft = $this->persistDraft();
        $settings = app(Settings::class);
        $before = app(ThemeManager::class)->published();

        $settings->set('theme', 'published', $draft);

        app(AuditLogger::class)->log('theme.published', null, $before, $draft, 'Website theme published');

        Notification::make()->title('Theme published')->success()->send();
    }

    /**
     * Validate and store the form as the draft theme.
     *
     * @return array<string, mixed>
     */
    protected function persistDraft(): array
    {
        abort_unless(static::canAccess(), 403);

        $theme = app(ThemeManager::class)->normalize(self::scalarState($this->form->getState()));
        app(Settings::class)->set('theme', 'draft', $theme);

        return $theme;
    }

    protected function hasUnpublishedChanges(): bool
    {
        $manager = app(ThemeManager::class);

        return $manager->draft() != $manager->published();
    }

    /**
     * @return array<int, string>
     */
    protected function warningsFor(Get $get): array
    {
        return app(ThemeManager::class)->contrastWarnings(self::scalarState([
            'default_mode' => $get('default_mode'),
            'allow_switcher' => $get('allow_switcher'),
            'radius' => $get('radius'),
            'light' => (array) $get('light'),
            'dark' => (array) $get('dark'),
        ]));
    }

    /**
     * Enum-backed fields may hold enum instances; the theme store keeps scalars.
     *
     * @param  array<string, mixed>  $state
     * @return array<string, mixed>
     */
    private static function scalarState(array $state): array
    {
        if (($state['default_mode'] ?? null) instanceof BackedEnum) {
            $state['default_mode'] = $state['default_mode']->value;
        }

        return $state;
    }

    protected function contrastSummary(Get $get): HtmlString
    {
        $warnings = $this->warningsFor($get);

        if ($warnings === []) {
            return new HtmlString('All checked colour pairs meet WCAG 2.2 AA contrast (4.5:1) in both modes.');
        }

        return new HtmlString('Some colour pairs fall below WCAG 2.2 AA:<ul class="mt-2 list-disc ps-5">'
            .collect($warnings)->map(fn (string $warning) => '<li>'.e($warning).'</li>')->implode('')
            .'</ul>');
    }

    private static function colorHelp(string $key): string
    {
        return match ($key) {
            'primary' => 'Buttons, links and key accents.',
            'secondary' => 'Supporting accents and info states.',
            'accent' => 'Highlights and calls to attention.',
            'highlight' => 'Gradient partner for the primary colour.',
            'background' => 'Page background.',
            'surface' => 'Cards, panels and muted bands.',
            'foreground' => 'Body text.',
            'muted' => 'Secondary text.',
            'border' => 'Dividers and outlines.',
            default => '',
        };
    }
}
