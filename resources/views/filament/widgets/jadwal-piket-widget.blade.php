<x-filament-widgets::widget class="fi-wi-jadwal-piket">
    <x-filament::section>
        <!-- Explicit Header with Strong Styling -->
        <div class="mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <span class="text-xl">📅</span>
                Jadwal Piket {{ $this->getData()['bulan'] }}
            </h3>
        </div>

        <div class="space-y-2">
            @foreach($this->getData()['jadwal'] as $item)
                <div class="flex items-center justify-between py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-6 h-6 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded-full text-xs font-bold">
                            {{ $item['minggu'] }}
                        </span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            Minggu {{ $item['minggu'] }}
                        </span>
                    </div>
                    <span class="text-sm text-gray-600 dark:text-gray-400 truncate max-w-32">
                        {{ $item['nama'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>