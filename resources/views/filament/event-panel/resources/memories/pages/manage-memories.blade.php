<x-filament-panels::page>
    <form wire:submit.prevent="create" class="fi-sc  fi-sc-has-gap fi-grid">
        {{ $this->form }}
        <div class="fi-ac fi-align-start">
            <button type="submit" class="fi-color fi-color-primary fi-bg-color-400 hover:fi-bg-color-300 dark:fi-bg-color-600 dark:hover:fi-bg-color-700 fi-text-color-950 hover:fi-text-color-800 dark:fi-text-color-0 dark:hover:fi-text-color-0 fi-btn fi-size-md  fi-ac-btn-action">
                Dodaj wspomnienie
            </button>
        </div>
    </form>
    <div class="custom-section memories-table">
        <h2 class="fi-header-heading">
            Last memories</h2>
        {{ $this->table }}
    </div>
</x-filament-panels::page>