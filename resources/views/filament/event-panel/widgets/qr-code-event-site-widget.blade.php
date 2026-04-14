     @php
     $event = filament()->getTenant();
     $slug = $event->eventPage?->slug;
     $url = $slug ? route('public.event.show', ['slug' => $slug]) : null;
     @endphp
     @if($url)
     <x-filament-widgets::widget>
         <x-filament::section>
             <x-slot name="heading">
                 {{ __('Event site QrCode') }}
             </x-slot>

             <div class="qr-code-wrapper">

                 {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(1000)
                 ->style('round')
                 ->margin(1)
                 ->generate($url) !!}

             </div>
             <div class="">
                 <x-filament::button
                     tag="a"
                     :href="route('qr.pdf', ['url' => url('/event/' . filament()->getTenant()->id.'memories')])"
                     icon="heroicon-o-arrow-down-tray">
                     {{ __('Download') }}
                 </x-filament::button>
             </div>
         </x-filament::section>
     </x-filament-widgets::widget>
     @else
      <x-filament-widgets::widget>
         <x-filament::section>
            <x-slot name="heading">
                 {{ __('Event site QrCode') }}
             </x-slot>
            <p>{{__('Not created yet')}}</p>
         </x-filament::section>
     </x-filament-widgets::widget>
     @endif
 