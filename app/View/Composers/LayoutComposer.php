<?php

namespace App\View\Composers;

use App\Http\Controllers\CookieConsentController;
use App\Models\Announcement;
use App\Support\Forms\SpamGuard;
use App\Support\SiteNavigation;
use App\Support\Theme\MotionConfig;
use App\Support\Theme\ThemeManager;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

/**
 * Shared data for the public layout (navigation, theme, motion, consent, ...).
 */
class LayoutComposer
{
    public function __construct(
        private SiteNavigation $navigation,
        private ThemeManager $theme,
        private MotionConfig $motion,
        private Request $request,
    ) {}

    public function compose(View $view): void
    {
        $navigation = $this->navigation->get();

        try {
            $announcement = Announcement::query()->current()->ordered()->first();
        } catch (Throwable) {
            $announcement = null;
        }

        $consent = CookieConsentController::current($this->request);
        $theme = $this->theme->active();

        $view->with([
            'navigation' => $navigation['menus'],
            'megaMenu' => $navigation['mega'],
            'cookiePolicyUrl' => $navigation['cookie_policy_url'] ?? null,
            'announcement' => $announcement,
            'themeCss' => $this->theme->css($theme),
            'themeDefault' => $theme['default_mode'],
            'themeSwitcher' => $theme['allow_switcher'],
            'isThemePreview' => $this->theme->isPreviewing(),
            'motion' => $this->motion->toArray(),
            'consent' => $consent,
            'analyticsAllowed' => ($consent['analytics'] ?? false) === true,
            'ga4' => $this->validId((string) settings('integrations.ga4_measurement_id'), '/^G-[A-Z0-9]{4,12}$/'),
            'gtm' => $this->validId((string) settings('integrations.gtm_container_id'), '/^GTM-[A-Z0-9]{4,10}$/'),
            'cookieBanner' => (bool) settings('privacy.cookie_banner_enabled', true),
            'spam' => app(SpamGuard::class),
        ]);
    }

    private function validId(string $value, string $pattern): ?string
    {
        return preg_match($pattern, $value) === 1 ? $value : null;
    }
}
