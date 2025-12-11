<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Web middleware group (includes session)
        // Middleware temporarily disabled for debugging
        
        // Filament Admin Middleware Stack
        $middleware->group('filament', [
            \App\Http\Middleware\FilamentSecurityEnhancer::class,
            \App\Http\Middleware\FilamentPerformanceOptimizer::class,
            \App\Http\Middleware\FilamentCsrfHandler::class,
        ]);
        
        // Alias middleware
        $middleware->alias([
            'filament.security' => \App\Http\Middleware\FilamentSecurityEnhancer::class,
            'filament.performance' => \App\Http\Middleware\FilamentPerformanceOptimizer::class,
            'filament.csrf' => \App\Http\Middleware\FilamentCsrfHandler::class,
            'webview.csrf' => \App\Http\Middleware\WebViewCsrfHandler::class,
            'api.security' => \App\Http\Middleware\ApiSecurityMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
