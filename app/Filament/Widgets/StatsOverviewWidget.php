<?php

namespace App\Filament\Widgets;

use App\Models\PengajuanIzin;
use App\Models\Presensi;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 0;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static string $view = 'filament.widgets.stats-overview-widget';

    protected function getHeading(): ?string
    {
        return null;
    }

    public function getDescription(): ?string
    {
        return null;
    }


}