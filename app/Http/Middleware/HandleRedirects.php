<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Applies administrator-managed redirects (301/302) and 410 Gone responses
 * from a cached lookup table before routing.
 */
class HandleRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethodSafe() || $request->is('admin', 'admin/*', 'livewire/*', 'build/*', 'storage/*')) {
            return $next($request);
        }

        try {
            $map = Redirect::map();
        } catch (Throwable) {
            return $next($request);
        }

        $path = Redirect::normalizePath($request->getPathInfo());

        if (! isset($map[$path])) {
            return $next($request);
        }

        [$target, $status, $id] = $map[$path];

        DB::table('redirects')->where('id', $id)->update([
            'hits' => DB::raw('hits + 1'),
            'last_hit_at' => now(),
        ]);

        if ($status === 410 || blank($target)) {
            abort(410);
        }

        $query = $request->getQueryString();
        $url = str_starts_with($target, 'http') ? $target : url($target);

        return redirect()->to($query ? $url.(str_contains($url, '?') ? '&' : '?').$query : $url, $status);
    }
}
