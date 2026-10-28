<?php

use App\Http\Controllers\ArtController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cookie-less asset routes (no session, no CSRF) — safe to cache on a CDN.
|--------------------------------------------------------------------------
*/

Route::get('/art/{seed}.svg', ArtController::class)
    ->where('seed', '[a-z0-9-]{1,120}')
    ->middleware('throttle:300,1')
    ->name('art');
