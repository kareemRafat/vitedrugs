<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetPartTheme
{
    /**
     * Ordered: the first entry is also the fallback part for shared routes.
     */
    protected const PARTS = ['drugs', 'large-animals', 'poultry'];

    public function handle(Request $request, Closure $next): Response
    {
        $part = $this->resolvePart($request);

        if ($part !== null) {
            session(['part' => $part]);
        } else {
            $part = $request->session()->get('part', self::PARTS[0]);

            if (! in_array($part, self::PARTS, true)) {
                $part = self::PARTS[0];
            }
        }

        View::share('part', $part);
        $request->attributes->set('part', $part);

        return $next($request);
    }

    protected function resolvePart(Request $request): ?string
    {
        $segments = $request->segments();

        $supportedLocales = array_keys(config('laravellocalization.supportedLocales', []));

        if ($segments !== [] && in_array($segments[0], $supportedLocales, true)) {
            array_shift($segments);
        }

        $first = $segments[0] ?? null;

        return in_array($first, self::PARTS, true) ? $first : null;
    }
}
