<?php

namespace App\Http\Controllers;

use App\Support\Theme\ThemeManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Lets authorised administrators view the public site with the draft theme.
 */
class ThemePreviewController extends Controller
{
    public function start(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('appearance.manage'), 403);

        $request->session()->put(ThemeManager::PREVIEW_SESSION_KEY, true);

        return redirect()->route('home');
    }

    public function stop(Request $request): RedirectResponse
    {
        $request->session()->forget(ThemeManager::PREVIEW_SESSION_KEY);

        return redirect()->to(rescue(fn () => route('filament.admin.pages.theme-settings'), url('/admin'), false));
    }
}
