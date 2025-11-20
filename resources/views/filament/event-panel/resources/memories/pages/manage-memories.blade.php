<x-filament-panels::page>
    <form wire:submit.prevent="create">
        {{ $this->form }}
        <button type="submit" class="mt-6 inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
            Dodaj wspomnienie
        </button>
    </form>
    {{ $this->table }}
</x-filament-panels::page>