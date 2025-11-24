<x-filament-panels::page>
    <style>
        .fi-ta-search-field {
            width: 100% !important;
            max-width: 500px !important;
            margin: 0 auto !important;
        }
        .fi-ta-header-toolbar {
            justify-content: center !important;
        }
    </style>

    @if (count($tabs = $this->getTabs()))
        <x-filament::tabs>
            @foreach ($tabs as $tabKey => $tab)
                @php
                    $activeTab = strval($this->activeTab);
                    $tabKey = strval($tabKey);
                @endphp
                <x-filament::tabs.item
                    :active="$activeTab === $tabKey"
                    :badge="$tab->getBadge()"
                    :icon="$tab->getIcon()"
                    :icon-position="$tab->getIconPosition()"
                    :wire:click="'$set(\'activeTab\', \'' . $tabKey . '\')'"
                >
                    {{ $tab->getLabel() ?? $tabKey }}
                </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>

        {{-- Loading indicator pour les tabs --}}
        <div wire:loading wire:target="activeTab" class="mt-2">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Chargement...</span>
            </div>
        </div>
    @endif

    {{-- Skeleton loader pendant le chargement --}}
    <div wire:loading.delay class="space-y-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        @for($i = 0; $i < 6; $i++)
            <x-skeleton-cycle-card />
        @endfor
    </div>

    {{-- Table réelle (cachée pendant le chargement) --}}
    <div wire:loading.remove.delay>
        {{ $this->table }}
    </div>

    {{-- Floating Action Button --}}
    <div style="position: fixed; bottom: 24px; right: 24px; z-index: 50;">
        <button type="button"
                wire:click="mountAction('create')"
                wire:loading.attr="disabled"
                wire:target="mountAction"
                style="display: flex; align-items: center; justify-content: center; width: 56px; height: 56px; background-color: #10b981; border-radius: 50%; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25); border: none; cursor: pointer; transition: transform 0.2s, background-color 0.2s;"
                onmouseover="this.style.backgroundColor='#059669'; this.style.transform='scale(1.1)';"
                onmouseout="this.style.backgroundColor='#10b981'; this.style.transform='scale(1)';">
            {{-- Icône normale --}}
            <span wire:loading.remove wire:target="mountAction">
                <x-heroicon-o-plus style="width: 28px; height: 28px; color: white;" />
            </span>
            {{-- Spinner pendant le chargement --}}
            <span wire:loading wire:target="mountAction">
                <svg class="animate-spin" style="width: 24px; height: 24px; color: white;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
        </button>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
