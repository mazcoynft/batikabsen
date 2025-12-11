<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Hide only THIS widget's default heading --}}
        <style>
            /* Hide only stats overview widget headers, not all widgets */
            .fi-wi-stats-overview .fi-wi-header,
            .fi-wi-stats-overview .fi-section-header,
            .fi-wi-stats-overview .fi-section-header-heading,
            [data-widget="stats-overview"] .fi-wi-header,
            .fi-wi-stats-overview-widget .fi-wi-header {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Ensure our custom content is visible */
            .stats-grid {
                margin-top: 0 !important;
            }
            
            /* Hide page title Dashboard */
            .fi-header-heading,
            .fi-page-header-heading,
            .fi-page-header h1,
            .fi-page h1,
            h1:contains("Dashboard") {
                display: none !important;
            }
        </style>
        
        {{-- JavaScript to hide Dashboard title --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Hide any element containing "Dashboard" text
                const elements = document.querySelectorAll('h1, h2, h3, .fi-header-heading, .fi-page-header-heading');
                elements.forEach(element => {
                    if (element.textContent.trim() === 'Dashboard') {
                        element.style.display = 'none';
                        element.style.visibility = 'hidden';
                        element.style.height = '0';
                        element.style.margin = '0';
                        element.style.padding = '0';
                    }
                });
                
                // Also try to hide parent containers
                setTimeout(() => {
                    const dashboardElements = document.querySelectorAll('*');
                    dashboardElements.forEach(element => {
                        if (element.textContent && element.textContent.trim() === 'Dashboard' && element.children.length === 0) {
                            element.style.display = 'none';
                        }
                    });
                }, 100);
            });
        </script>
        {{-- Load Dashboard Fix CSS --}}
        <link rel="stylesheet" href="{{ asset('css/dashboard-fix.css') }}">
        
        {{-- Preload critical resources --}}
        <link rel="preload" href="{{ asset('js/csrf-handler.js') }}" as="script">
        
        {{-- Custom CSS --}}
        <style>
            /* Dashboard ABSEN-BATIK - Enhanced Styles */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 0.75rem;
                margin-top: 0.5rem;
                margin-bottom: 0.5rem;
            }
            
            /* Fix widget container to prevent overlap */
            .fi-wi-stats-overview-widget,
            .fi-widget {
                margin-bottom: 1rem !important;
            }
            
            /* Ensure proper spacing between widgets */
            .fi-wi-stats-overview .fi-section {
                margin-bottom: 0.5rem !important;
            }
            
            /* Ensure other widgets are not affected */
            .fi-wi-jadwal-piket .fi-section-header,
            .fi-wi-uang-makan-chart .fi-section-header,
            .fi-wi-jadwal-piket h3,
            .fi-wi-uang-makan-chart h3 {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            .stat-card {
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
                border-radius: 8px;
                padding: 0.75rem;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
                border: 1px solid rgba(0, 0, 0, 0.05);
                min-height: 70px;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            
            .stat-card:hover {
                transform: translateY(-4px) scale(1.02);
                box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
            }
            
            .stat-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: var(--card-accent, #3b82f6);
                border-radius: 16px 16px 0 0;
            }
            
            .stat-card.success::before { --card-accent: #10b981; }
            .stat-card.warning::before { --card-accent: #f59e0b; }
            .stat-card.danger::before { --card-accent: #ef4444; }
            .stat-card.gray::before { --card-accent: #6b7280; }
            
            .stat-content {
                display: flex;
                align-items: center;
                gap: 0.25rem;
                flex: 1;
            }
            
            .stat-icon {
                width: 32px;
                height: 32px;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1rem;
                transition: all 0.3s ease;
                flex-shrink: 0;
            }
            
            .stat-card:hover .stat-icon {
                transform: scale(1.1) rotate(5deg);
            }
            
            .stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
            .stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
            .stat-icon.danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
            .stat-icon.gray { background: linear-gradient(135deg, #6b7280, #4b5563); }
            
            .stat-value {
                font-size: 1.5rem;
                font-weight: 700;
                line-height: 1;
                color: #1f2937;
                margin-right: 0.25rem;
            }
            
            .stat-label {
                font-size: 0.75rem;
                font-weight: 500;
                color: #374151;
                line-height: 1.2;
            }
            

            
            /* Responsive */
            @media (max-width: 1200px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 0.5rem;
                }
            }
            
            @media (max-width: 768px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 0.5rem;
                }
                
                .stat-card {
                    padding: 0.5rem;
                    min-height: 60px;
                }
                
                .stat-value {
                    font-size: 1.25rem;
                }
                
                .stat-label {
                    font-size: 0.625rem;
                }
                
                .stat-icon {
                    width: 24px;
                    height: 24px;
                    font-size: 0.875rem;
                }
            }
            
            @media (max-width: 480px) {
                .stats-grid {
                    grid-template-columns: 1fr;
                    gap: 0.5rem;
                }
            }
        </style>

        <div>
            <h6 class="text-sm font-bold text-gray-900 mb-1">
                📅 Kehadiran Hari Ini - {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
            </h6>
            
            <div class="stats-grid">
                @php
                    // Use optimized service for better performance
                    $stats = \App\Services\FilamentOptimizationService::getDashboardStats();
                    $hadir = $stats['hadir'];
                    $terlambat = $stats['terlambat'];
                    $izin = $stats['izin'];
                    $sakit = $stats['sakit'];
                @endphp
                
                {{-- Hadir Card --}}
                <div class="stat-card success">
                    <div class="stat-icon success">👥</div>
                    <div class="stat-content">
                        <div class="stat-label">Hadir Tepat Waktu</div>
                        <div class="stat-value">{{ $hadir }}</div>
                    </div>
                </div>
                
                {{-- Izin/Cuti Card --}}
                <div class="stat-card warning">
                    <div class="stat-icon warning">📋</div>
                    <div class="stat-content">
                        <div class="stat-label">Izin/Cuti</div>
                        <div class="stat-value">{{ $izin }}</div>
                    </div>
                </div>
                
                {{-- Sakit Card --}}
                <div class="stat-card danger">
                    <div class="stat-icon danger">💊</div>
                    <div class="stat-content">
                        <div class="stat-label">Sakit</div>
                        <div class="stat-value">{{ $sakit }}</div>
                    </div>
                </div>
                
                {{-- Terlambat Card --}}
                <div class="stat-card gray">
                    <div class="stat-icon gray">⏰</div>
                    <div class="stat-content">
                        <div class="stat-label">Terlambat</div>
                        <div class="stat-value">{{ $terlambat }}</div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>