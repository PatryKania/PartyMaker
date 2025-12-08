<?php

namespace App\Providers\Filament;

use App\Filament\Helper\CustomLogin;
use App\Filament\Helper\CustomRegister;
use App\Filament\Widgets\EventCreateWidget;
use App\Filament\Widgets\FakeEventStats;
use App\Filament\Widgets\FakeStats;
use App\Filament\Widgets\FakeUserStats;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Facades\FilamentColor;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        FilamentColor::register([
            'primary' => [
                50 => 'oklch(0.9694 0.0169 50.44)',
                100 => 'oklch(0.8746 0.066 54.77)',
                200 => 'oklch(0.778 0.0662 54.31)',
                300 => 'oklch(0.6806 0.0656 54.4)',
                400 => 'oklch(0.5843 0.0662 53.76)',
                500 => 'oklch(0.4888 0.0696 54.06)',
                600 => 'oklch(0.4335 0.095 53.84)',
                700 => 'oklch(0.358 0.0891 54.53)',
                800 => 'oklch(0.279 0.0698 54.09)',
                900 => 'oklch(0.2095 0.0526 53.89)',
                950 => 'oklch(0.2095 0.0526 53.89)',
            ],
        ]);

        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->brandName('PartyMaker')
            ->darkMode(false)
            ->login(CustomLogin::class)
            ->registration(CustomRegister::class)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                EventCreateWidget::class,
                FakeStats::class,
                FakeUserStats::class,
                FakeEventStats::class,
            ])
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
            ])
            ->resourceCreatePageRedirect('index')
            ->authMiddleware([
                Authenticate::class,
            ])->topNavigation()->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn(): string => Blade::render('
                    <x-filament::button
                        color="primary"
                        tag="a"
                        size="xl"
                        href="' . route('filament.dashboard.resources.events.create') . '"
                        class="cta-widget-btn ml-6" outlined
                    >
                       ' . __('Create event') . '
                    </x-filament::button>
                ')

            );
    }
}
