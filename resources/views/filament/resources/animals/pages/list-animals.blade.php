<x-filament-panels::page>
    <style>
        /* Centrer le champ de recherche */
        .fi-ta-search-field {
            width: 100% !important;
            max-width: 500px !important;
            margin: 0 auto !important;
        }
        .fi-ta-header-toolbar {
            justify-content: center !important;
        }
    </style>

    {{ $this->table }}

    {{-- Floating Action Button --}}
    <div style="position: fixed; bottom: 24px; right: 24px; z-index: 50;">
        <button type="button"
                wire:click="mountAction('create')"
                style="display: flex; align-items: center; justify-content: center; width: 56px; height: 56px; background-color: #10b981; border-radius: 50%; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25); border: none; cursor: pointer; transition: transform 0.2s, background-color 0.2s;"
                onmouseover="this.style.backgroundColor='#059669'; this.style.transform='scale(1.1)';"
                onmouseout="this.style.backgroundColor='#10b981'; this.style.transform='scale(1)';">
            <x-heroicon-o-plus style="width: 28px; height: 28px; color: white;" />
        </button>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
