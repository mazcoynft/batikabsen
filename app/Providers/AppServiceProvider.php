<?php

namespace App\Providers;

use App\Models\User;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Illuminate\Contracts\View\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        parent::register();
        FilamentView::registerRenderHook('panels::body.end', fn(): string => Blade::render("@vite('resources/js/app.js')"));
    }

    public function boot(): void
    {
        FilamentAsset::register([
            Css::make('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'),
            Js::make('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'),
            // Tambahkan SweetAlert2
            Css::make('sweetalert2-css', 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'),
            Js::make('sweetalert2-js', 'https://cdn.jsdelivr.net/npm/sweetalert2@11'),
        ]);

        FilamentView::registerRenderHook(
            'panels::auth.login.form.after',
            fn (): View => view('filament.login_extra')
        );
    }
}
