<x-filament-widgets::widget class="fi-wi-uang-makan-chart">
    <x-filament::section>
        <!-- Explicit Header with Strong Styling -->
        <div class="mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <span class="text-xl">💰</span>
                Uang Makan Per Karyawan
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Periode: {{ $this->getData()['periode'] }}
            </p>
        </div>

        @php
            $chartData = $this->getData();
            $karyawanData = $chartData['karyawan'];
            $pieLabels = $chartData['pieLabels'];
            $pieData = $chartData['pieData'];
            $totalUangMakan = $chartData['totalUangMakan'];
            $maxUangMakan = $chartData['maxUangMakan'];
            $chartId = 'uang-makan-pie-' . uniqid();
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Pie Chart --}}
            <div class="bg-white dark:bg-gray-900 rounded-lg p-3">
                <div class="relative" style="height: 200px;">
                    <canvas id="{{ $chartId }}"></canvas>
                </div>
            </div>
            
            {{-- Karyawan List with Progress Bars --}}
            <div class="space-y-2">
                @foreach($karyawanData as $index => $karyawan)
                    @php
                        $percentage = $maxUangMakan > 0 ? ($karyawan['uang_makan'] / $maxUangMakan) * 100 : 0;
                        $colors = [
                            'bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-yellow-500', 'bg-red-500',
                            'bg-indigo-500', 'bg-pink-500', 'bg-teal-500', 'bg-orange-500', 'bg-cyan-500'
                        ];
                        $color = $colors[$index % count($colors)];
                    @endphp
                    
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                {{ $karyawan['nama'] }}
                            </span>
                            <span class="text-xs text-gray-600 dark:text-gray-400">
                                {{ $karyawan['hari_hadir'] }} hari
                            </span>
                        </div>
                        
                        {{-- Progress Bar --}}
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-2">
                            <div class="{{ $color }} h-2 rounded-full transition-all duration-500 ease-out" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-900 dark:text-gray-100">
                                Rp {{ number_format($karyawan['uang_makan'], 0, ',', '.') }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ number_format($percentage, 1) }}%
                            </span>
                        </div>
                    </div>
                @endforeach
                
                @if(empty($karyawanData))
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <span class="text-2xl mb-2 block">📊</span>
                        <p class="text-sm">Belum ada data uang makan minggu ini</p>
                    </div>
                @endif
            </div>
        </div>
        
        {{-- Total Summary --}}
        <div class="mt-4 text-center p-3 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 rounded-lg">
            <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">
                Total Uang Makan Minggu Ini
            </div>
            <div class="text-xl font-bold text-gray-900 dark:text-gray-100">
                Rp {{ number_format($totalUangMakan, 0, ',', '.') }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                {{ count($karyawanData) }} karyawan aktif
            </div>
        </div>

        {{-- Chart.js Script --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('{{ $chartId }}').getContext('2d');
                
                const colors = [
                    '#3B82F6', '#10B981', '#8B5CF6', '#F59E0B', '#EF4444',
                    '#6366F1', '#EC4899', '#14B8A6', '#F97316', '#06B6D4'
                ];
                
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: @json($pieLabels),
                        datasets: [{
                            data: @json($pieData),
                            backgroundColor: colors.slice(0, @json(count($pieLabels))),
                            borderColor: '#ffffff',
                            borderWidth: 2,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return context.label + ': Rp ' + context.parsed.toLocaleString('id-ID') + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1000,
                            easing: 'easeInOutQuart'
                        }
                    }
                });
            });
        </script>
    </x-filament::section>
</x-filament-widgets::widget>