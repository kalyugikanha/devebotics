<?php

namespace App\Filament\Pages\Settings;

use App\Filament\AdminNavigationGroup;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Third-party integration settings. Only public identifiers are stored here;
 * secrets (CAPTCHA secret, API keys, SMTP password) stay in the environment
 * and are only ever reported as configured / not configured.
 */
class IntegrationSettings extends SettingsPage
{
    protected static string $permission = 'integrations.manage';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Settings;

    protected static ?int $navigationSort = 30;

    protected static ?string $navigationLabel = 'Integrations';

    protected static ?string $slug = 'settings/integrations';

    protected static function settingsGroups(): array
    {
        return ['integrations'];
    }

    public static function turnstileConfigured(): bool
    {
        return filled(config('softec.turnstile.site_key')) && filled(config('softec.turnstile.secret_key'));
    }

    public static function anthropicConfigured(): bool
    {
        return filled(config('softec.ai.anthropic_api_key'));
    }

    protected function formComponents(): array
    {
        return [
            Callout::make('Secrets are never shown here')
                ->description('API keys, the CAPTCHA secret and SMTP credentials are configured in the server environment (.env). This page only shows whether they are present.')
                ->info(),
            Section::make('Analytics')
                ->description('Analytics tags load only after visitors accept analytics cookies.')
                ->columns(2)
                ->schema([
                    TextInput::make('integrations.ga4_measurement_id')->label('Google Analytics 4 measurement ID')->placeholder('G-XXXXXXXXXX')
                        ->regex('/^G-[A-Z0-9]{4,12}$/')->validationMessages(['regex' => 'Expected a GA4 measurement ID such as G-AB12CD34EF.']),
                    TextInput::make('integrations.gtm_container_id')->label('Google Tag Manager container ID')->placeholder('GTM-XXXXXXX')
                        ->regex('/^GTM-[A-Z0-9]{4,10}$/')->validationMessages(['regex' => 'Expected a container ID such as GTM-AB12CD3.']),
                ]),
            Section::make('Bot protection & AI')->columns(2)->schema([
                Toggle::make('integrations.turnstile_enabled')
                    ->label('Cloudflare Turnstile on public forms')
                    ->disabled(fn (): bool => ! self::turnstileConfigured())
                    ->helperText(fn (): string => self::turnstileConfigured()
                        ? 'Keys detected in the environment.'
                        : 'Set TURNSTILE_SITE_KEY and TURNSTILE_SECRET_KEY in the environment to enable.'),
                Toggle::make('integrations.ai_drafting_enabled')
                    ->label('AI-assisted blog drafting (drafts only)')
                    ->disabled(fn (): bool => ! self::anthropicConfigured())
                    ->helperText(fn (): string => self::anthropicConfigured()
                        ? 'AI suggestions are saved as drafts and always require human review.'
                        : 'Set ANTHROPIC_API_KEY in the environment to enable.'),
            ]),
            Section::make('Environment status')->schema([
                Grid::make(3)->schema([
                    IconEntry::make('status_turnstile')->label('Turnstile keys')->state(fn (): bool => self::turnstileConfigured())->boolean(),
                    IconEntry::make('status_ai')->label('Anthropic API key')->state(fn (): bool => self::anthropicConfigured())->boolean(),
                    IconEntry::make('status_s3')->label('S3 storage credentials')->state(fn (): bool => filled(config('filesystems.disks.s3.key')) && filled(config('filesystems.disks.s3.bucket')))->boolean(),
                    TextEntry::make('status_mail')->label('Mail transport')->state(fn (): string => (string) config('mail.default')),
                    TextEntry::make('status_media')->label('Media storage disk')->state(fn (): string => (string) config('softec.media.disk')),
                    TextEntry::make('status_queue')->label('Queue connection')->state(fn (): string => (string) config('queue.default')),
                ]),
            ]),
        ];
    }

    protected function mutateGroupBeforeSave(string $group, array $values): array
    {
        $values = parent::mutateGroupBeforeSave($group, $values);

        // Toggles cannot be enabled server-side without the required environment secrets.
        if (! self::turnstileConfigured()) {
            $values['turnstile_enabled'] = false;
        }

        if (! self::anthropicConfigured()) {
            $values['ai_drafting_enabled'] = false;
        }

        foreach (['turnstile_enabled', 'ai_drafting_enabled'] as $key) {
            $values[$key] = (bool) ($values[$key] ?? false);
        }

        return $values;
    }
}
