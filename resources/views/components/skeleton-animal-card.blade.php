{{-- Skeleton loader pour une carte animal --}}
<div class="flex items-center gap-3 p-4 animate-pulse">
    {{-- Avatar skeleton --}}
    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-xl flex-shrink-0"></div>

    {{-- Contenu skeleton --}}
    <div class="flex-1 space-y-2">
        {{-- Ligne 1: Numéro + badge --}}
        <div class="flex items-center gap-2">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
            <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
        </div>

        {{-- Ligne 2: Détails --}}
        <div class="flex items-center gap-2">
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
        </div>
    </div>

    {{-- Chevron skeleton --}}
    <div class="w-5 h-5 bg-gray-200 dark:bg-gray-700 rounded flex-shrink-0"></div>
</div>
