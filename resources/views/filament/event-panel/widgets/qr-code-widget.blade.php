<x-filament-widgets::widget>

    <x-filament::section>
        <x-slot name="heading">
            {{ __('Event QrCode') }}
        </x-slot>

        <div class="qr-code-wrapper">
            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size('1000')->generate(
            url('/event/' . filament()->getTenant()->id)
            ) !!}
        </div>
        <div class="">
            <x-filament::button
                tag="a"
                :href="route('qr.pdf', ['url' => url('/event/' . filament()->getTenant()->id)])"
                icon="heroicon-o-arrow-down-tray">
                {{ __('Download') }}
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>