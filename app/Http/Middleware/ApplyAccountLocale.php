<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplyAccountLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = Auth::user()?->locale;

        if (in_array($locale, ['en', 'pl'], true)) {
            app()->setLocale($locale);
        } else {
            app()->setLocale(config('app.locale'));
        }

        return $next($request);
    }
}
