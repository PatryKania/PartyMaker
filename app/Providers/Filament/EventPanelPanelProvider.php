<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use App\Http\Middleware\SetCurrentEvent;
use Filament\Actions\Action;
use App\Filament\EventPanel\Pages\EventDashboard;
use Filament\Navigation\NavigationItem;

class EventPanelPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        return $panel
            ->id('event')
            ->path('event/{event}')
            ->brandName('PartyMaker')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/EventPanel/Resources'), for: 'App\Filament\EventPanel\Resources')
            ->discoverPages(in: app_path('Filament/EventPanel/Pages'), for: 'App\Filament\EventPanel\Pages')
            ->pages([
                EventDashboard::class
            ])
            ->discoverWidgets(in: app_path('Filament/EventPanel/Widgets'), for: 'App\Filament\EventPanel\Widgets')
            ->widgets([])
            // ->navigationItems([
            //     NavigationItem::make('Dashboard')
            //         ->icon('heroicon-o-arrow-left')
            //         ->url(fn() => url('/dashboard'))
            // ])
            ->resourceCreatePageRedirect('index')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetCurrentEvent::class
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
