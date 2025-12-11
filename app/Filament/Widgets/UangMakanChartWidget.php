<?php

namespace App\Filament\Widgets;

use App\Models\Presensi;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class UangMakanChartWidget extends Widget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];
    
    protected static string $view = 'filament.widgets.uang-makan-chart-widget';

    public function getData(): array
    {
        // Use optimized service for better performance
        return \App\Services\FilamentOptimizationService::getUangMakanData();
    }
}