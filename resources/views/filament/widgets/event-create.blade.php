<x-filament-widgets::widget>
    <x-filament::section>
        <div class="cta-widget-wrapper">
            <h2 class="cta-widget-heading">
                Ready to host your next great event?
            </h2>
            <p class="cta-widget-txt">
                Inspire your audience and bring people together - start creating your event today!
            </p>
            <x-filament::button
                color="primary"
                tag="a"
                size="xl"
                href="{{ route('filament.dashboard.resources.events.create') }}"
                class="cta-widget-btn">
                Start create
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>