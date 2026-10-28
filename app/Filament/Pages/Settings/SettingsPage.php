<?php

namespace App\Filament\Pages\Settings;

use App\Support\Audit\AuditLogger;
use App\Support\Settings\Settings;
use App\Support\Settings\SettingsRegistry;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * Base class for admin settings pages. Values are read from and written to
 * the `settings` table through App\Support\Settings\Settings, restricted to
 * the keys declared in SettingsRegistry for the page's groups.
 *
 * @property-read Schema $form
 */
abstract class SettingsPage extends Page
{
    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    /**
     * Permission required to open and save this page.
     */
    protected static string $permission = 'settings.manage';

    /**
     * Settings groups edited on this page (keys of SettingsRegistry::defaults()).
     *
     * @return array<int, string>
     */
    abstract protected static function settingsGroups(): array;

    /**
     * @return array<int, mixed>
     */
    abstract protected function formComponents(): array;

    public static function canAccess(): bool
    {
        return (bool) Auth::user()?->can(static::$permission);
    }

    public function mount(): void
    {
        $this->form->fill($this->loadSettings());
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function loadSettings(): array
    {
        $settings = app(Settings::class);

        return collect(static::settingsGroups())
            ->mapWithKeys(fn (string $group) => [$group => $settings->group($group)])
            ->all();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->formComponents())
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make($this->getFormActions())->sticky()->key('form-actions'),
                ]),
        ]);
    }

    /**
     * @return array<int, Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label('Save settings')->submit('save')->keyBindings(['mod+s']),
        ];
    }

    public function save(): void
    {
        abort_unless(static::canAccess(), 403);

        $state = $this->form->getState();
        $settings = app(Settings::class);
        $changed = [];

        foreach (static::settingsGroups() as $group) {
            $allowedKeys = array_keys(SettingsRegistry::defaults()[$group] ?? []);
            $before = $settings->group($group);
            $values = Arr::only($this->mutateGroupBeforeSave($group, (array) ($state[$group] ?? [])), $allowedKeys);

            foreach ($values as $key => $value) {
                if (($before[$key] ?? null) !== $value) {
                    $changed[] = $group.'.'.$key;
                }
            }

            $settings->setMany($group, $values);
        }

        if ($changed !== []) {
            app(AuditLogger::class)->log('settings.updated', null, null, ['keys' => $changed], static::getNavigationLabel().' settings updated ('.count($changed).' change(s))');
        }

        $this->afterSave();

        Notification::make()->title('Settings saved')->success()->send();
    }

    /**
     * Normalise values before persisting (e.g. empty strings → null).
     *
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    protected function mutateGroupBeforeSave(string $group, array $values): array
    {
        return array_map(fn ($value) => $value === '' ? null : $value, $values);
    }

    protected function afterSave(): void {}
}
