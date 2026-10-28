<?php

namespace App\Filament\Pages\Settings;

use App\Filament\AdminNavigationGroup;
use App\Support\Theme\MotionConfig;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Whitelisted motion presets only — administrators never enter code.
 * Visitors who prefer reduced motion always get a static experience.
 */
class AnimationSettings extends SettingsPage
{
    protected static string $permission = 'appearance.manage';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Appearance;

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Animation';

    protected static ?string $title = 'Animation settings';

    protected static ?string $slug = 'settings/animation';

    protected static function settingsGroups(): array
    {
        return ['animation'];
    }

    protected function formComponents(): array
    {
        return [
            Callout::make('Accessibility first')
                ->description('Visitors with “reduce motion” enabled in their operating system, or who switch motion off on the website, always see a static version.')
                ->info(),
            Section::make('Global')->columns(2)->schema([
                Toggle::make('animation.enabled')->label('Enable website animations')->columnSpanFull(),
                Select::make('animation.intensity')->options(MotionConfig::INTENSITIES)->required()->selectablePlaceholder(false),
                TextInput::make('animation.duration_scale')
                    ->label('Duration scale')
                    ->numeric()
                    ->minValue(0.5)
                    ->maxValue(1.5)
                    ->step(0.1)
                    ->required()
                    ->helperText('1.0 is the default speed; lower is faster, higher is slower.'),
            ]),
            Section::make('Presets')->columns(2)->schema([
                Select::make('animation.hero_preset')->label('Hero headline')->options(MotionConfig::HERO_PRESETS)->required()->selectablePlaceholder(false),
                Select::make('animation.reveal_preset')->label('Scroll reveal')->options(MotionConfig::REVEAL_PRESETS)->required()->selectablePlaceholder(false),
            ]),
            Section::make('Effects')->schema([
                Grid::make(2)->schema([
                    Toggle::make('animation.stagger')->label('Staggered card entrances'),
                    Toggle::make('animation.parallax')->label('Gentle parallax on decorative elements'),
                    Toggle::make('animation.counters')->label('Animated statistic counters'),
                    Toggle::make('animation.ambient')->label('Ambient gradient background'),
                    Toggle::make('animation.marquee')->label('Scrolling logo carousel'),
                    Toggle::make('animation.decorative_on_mobile')->label('Decorative motion on mobile devices'),
                ]),
            ]),
        ];
    }

    protected function mutateGroupBeforeSave(string $group, array $values): array
    {
        $values = parent::mutateGroupBeforeSave($group, $values);
        $values['duration_scale'] = round(max(0.5, min(1.5, (float) ($values['duration_scale'] ?? 1.0))), 2);

        foreach (['enabled', 'stagger', 'parallax', 'counters', 'ambient', 'marquee', 'decorative_on_mobile'] as $flag) {
            $values[$flag] = (bool) ($values[$flag] ?? false);
        }

        $values['intensity'] = array_key_exists((string) $values['intensity'], MotionConfig::INTENSITIES) ? $values['intensity'] : 'standard';
        $values['hero_preset'] = array_key_exists((string) $values['hero_preset'], MotionConfig::HERO_PRESETS) ? $values['hero_preset'] : 'rise';
        $values['reveal_preset'] = array_key_exists((string) $values['reveal_preset'], MotionConfig::REVEAL_PRESETS) ? $values['reveal_preset'] : 'fade-up';

        return $values;
    }
}
