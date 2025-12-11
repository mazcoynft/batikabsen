<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;

class FilamentCsrfHandler
{
    /**
     * Handle an incoming request for Filament admin panel.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip CSRF for API routes and specific Filament routes
        if ($this->shouldSkipCsrf($request)) {
            return $next($request);
        }

        // Enhanced session management for Filament (only if session is available)
        if ($this->isFilamentRequest($request) && $request->hasSession()) {
            // Extend session lifetime for admin users
            config(['session.lifetime' => 720]); // 12 hours for admin
            
            // Regenerate session periodically
            if ($this->shouldRegenerateSession($request)) {
                $request->session()->regenerate();
            }
            
            // Auto-refresh CSRF token for long sessions
            if ($this->shouldRefreshToken($request)) {
                $request->session()->regenerateToken();
            }
        }

        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // Handle CSRF mismatch gracefully
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'CSRF token mismatch. Please refresh and try again.',
                    'csrf_token' => csrf_token(),
                    'redirect' => route('filament.admin.auth.login')
                ], 419);
            }

            // Redirect to login with message
            return redirect()->route('filament.admin.auth.login')
                ->with('error', 'Session expired. Please login again.')
                ->with('csrf_token', csrf_token());
        }
    }

    /**
     * Check if request should skip CSRF verification
     */
    private function shouldSkipCsrf(Request $request): bool
    {
        $skipRoutes = [
            'livewire/message/*',
            'admin/livewire/message/*',
            'filament/livewire/message/*'
        ];

        foreach ($skipRoutes as $route) {
            if ($request->is($route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if request is for Filament admin panel
     */
    private function isFilamentRequest(Request $request): bool
    {
        return $request->is('admin/*') || 
               $request->is('filament/*') ||
               str_contains($request->path(), 'admin');
    }

    /**
     * Check if session should be regenerated
     */
    private function shouldRegenerateSession(Request $request): bool
    {
        if (!$request->hasSession()) {
            return false;
        }
        
        $lastRegeneration = $request->session()->get('last_regeneration', 0);
        return (time() - $lastRegeneration) > 1800; // 30 minutes
    }

    /**
     * Check if CSRF token should be refreshed
     */
    private function shouldRefreshToken(Request $request): bool
    {
        if (!$request->hasSession()) {
            return false;
        }
        
        $lastRefresh = $request->session()->get('last_token_refresh', 0);
        return (time() - $lastRefresh) > 3600; // 1 hour
    }
}