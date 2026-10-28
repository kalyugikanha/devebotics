<?php

namespace App\Providers\Filament;

use App\Filament\AdminNavigationGroup;
use App\Filament\Pages\Auth\RequestPasswordReset;
use App\Http\Middleware\AuthenticateAdmin;
use App\Support\Theme\AdminPalette;
use App\Support\Theme\ThemeManager;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Enums\ThemeMode;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset(RequestPasswordReset::class)
            ->profile(isSimple: false)
            ->multiFactorAuthentication([
                AppAuthentication::make()->recoverable(),
                EmailAuthentication::make(),
            ])
            // Every resource must have a policy; missing policies deny access.
            ->strictAuthorization()
            ->brandName(fn (): string => (string) settings('company.name'))
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('2.25rem')
            ->favicon(asset('images/brand/favicon-32.png'))
            ->colors(fn (): array => $this->brandColors())
            ->font('Inter Variable', provider: LocalFontProvider::class)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->defaultThemeMode(ThemeMode::System)
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups(AdminNavigationGroup::class)
            ->databaseNotifications()
            ->databaseNotificationsPolling('60s')
            ->unsavedChangesAlerts()
            ->databaseTransactions()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): string => Blade::render('<x-filament::link :href="url(\'/\')" target="_blank" rel="noopener" icon="heroicon-m-arrow-top-right-on-square" class="me-4 hidden text-sm sm:inline-flex">View website</x-filament::link>'),
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                // Filament's Authenticate + immediate sign-out of deactivated accounts.
                AuthenticateAdmin::class,
            ]);
    }

    /**
     * Admin colours follow the published website theme (light palette).
     *
     * @return array<string, array<int|string, string>>
     */
    private function brandColors(): array
    {
        $theme = app(ThemeManager::class)->published();

        return [
            'primary' => AdminPalette::fromHex($theme['light']['primary']),
            'info' => Color::hex($theme['light']['secondary']),
            'warning' => Color::Amber,
            'danger' => Color::Rose,
            'success' => Color::Emerald,
            'gray' => Color::Slate,
        ];
    }
}
