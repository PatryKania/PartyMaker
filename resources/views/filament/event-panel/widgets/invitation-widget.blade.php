<x-filament::widget>
    <x-filament::section>
        <div class="invitation-widget-wrapper">
            <h2 class="invitation-widget-heading">
                {!! __('Presence confirmation for <b>:name</b> on <b>:date</b>', [
                'name' => $event->name,
                'date' => \Carbon\Carbon::parse($event->date)->format('d.m.Y')
                ]) !!}
            </h2>

            <div class="invitation-widget-txt">
                {!! $event->invitation !!}
            </div>

            <div class="invitation-widget-btns">
                {{ ($this->rejectAction)(['record' => $event->id]) }}
                {{ ($this->confirmAction)(['record' => $event->id]) }}
            </div>
        </div>
        <x-filament-actions::modals />
    </x-filament::section>
</x-filament::widget>