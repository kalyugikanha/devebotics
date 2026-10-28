<?php

namespace App\Http\Middleware;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;

/**
 * Filament authentication that signs out deactivated accounts (and sends
 * them to the login page) instead of showing a 403 page. Runs on every
 * admin request, so a deactivation takes effect immediately.
 */
class AuthenticateAdmin extends Authenticate
{
    /**
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        $user = Filament::auth()->user();

        if ($user instanceof User && ! $user->is_active) {
            Filament::auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $this->unauthenticated($request, $guards);
        }

        parent::authenticate($request, $guards);
    }
}
