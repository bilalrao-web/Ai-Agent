<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ $this->getTitle() }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ now()->format('h:i A, jS M Y') }}
                </p>
            </div>
        </div>

        {{ $this->widgets }}
    </div>
</x-filament-panels::page>
