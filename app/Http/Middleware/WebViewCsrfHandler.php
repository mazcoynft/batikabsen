<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;

class WebViewCsrfHandler
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Detect if request is from WebView
        $userAgent = $request->header('User-Agent', '');
        $isWebView = $this->isWebViewRequest($userAgent);
        
        // If WebView and CSRF token mismatch, try to refresh token
        if ($isWebView && $request->hasSession() && $this->isTokenMismatch($request)) {
            // Regenerate CSRF token
            $request->session()->regenerateToken();
            
            // If it's an AJAX request, return JSON with new token
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'CSRF token mismatch',
                    'message' => 'Please refresh the page and try again',
                    'csrf_token' => csrf_token(),
                    'action' => 'refresh_token'
                ], 419);
            }
            
            // For regular requests, redirect back with new token
            return redirect()->back()
                ->with('error', 'Session expired. Please try again.')
                ->with('csrf_token', csrf_token());
        }
        
        return $next($request);
    }
    
    /**
     * Check if request is from WebView
     */
    private function isWebViewRequest(string $userAgent): bool
    {
        $webViewIndicators = [
            'wv', // Android WebView
            'WebView',
            'Mobile/',
            'Version/', // iOS WebView
            'Safari/' // But not full Safari
        ];
        
        foreach ($webViewIndicators as $indicator) {
            if (stripos($userAgent, $indicator) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if there's a token mismatch
     */
    private function isTokenMismatch(Request $request): bool
    {
        if (!$request->hasSession()) {
            return false; // Skip if no session
        }
        
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        
        if (!$token) {
            return true;
        }
        
        return !hash_equals($request->session()->token(), $token);
    }
}