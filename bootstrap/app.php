<?php

use App\Http\Middleware\HandleRedirects;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware([])->group(base_path('routes/assets.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Runs before routing so managed redirects also catch URLs that would 404.
        $middleware->prepend(HandleRedirects::class);
        $middleware->append(SecurityHeaders::class);

        // Only the consent cookie is read by JavaScript; it holds no personal data.
        $middleware->encryptCookies(except: ['softec_consent']);

        $middleware->redirectGuestsTo(fn () => route('filament.admin.auth.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Never flash secrets back into the session after validation errors.
        $exceptions->dontFlash(['password', 'password_confirmation', 'current_password']);
    })->create();
