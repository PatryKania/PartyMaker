<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Http\Request;

class ApplyEventThemeColors
{
    public function handle(Request $request, Closure $next)
    {

        $tenant = Filament::getTenant();
        if ($tenant && !empty($tenant->color)) {

            FilamentColor::register([
                'primary' => $tenant->color,
            ]);
        }

        return $next($request);
    }
}
