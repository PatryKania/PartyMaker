<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;

use Illuminate\Support\Facades\Notification;
use NotificationChannels\Smsapi\SmsapiChannel;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    protected $policies = [
        \App\Models\Participant::class => \App\Policies\ParticipantPolicy::class,
    ];

    public function register(): void
    {


        FilamentAsset::register([
            Css::make('custom-stylesheet', __DIR__ . '/../../resources/css/custom-styles.css'),
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            return $switch
                ->locales(['en', 'pl'])->flags([
                    'en' => asset('svg/flags/gb.svg'),
                    'pl' => asset('svg/flags/pl.svg'),
                ])->flagsOnly()->visible(outsidePanels: true);
        });
        Notification::extend('smsapi', function ($app) {
        return $app->make(SmsapiChannel::class);
    });
    }
}
