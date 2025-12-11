<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiSecurityMiddleware
{
    /**
     * Handle an incoming request for API/AJAX calls.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Enhanced security for frontend-backend communication
        if ($this->isApiRequest($request)) {
            // Validate request origin
            if (!$this->isValidOrigin($request)) {
                Log::warning('Invalid origin detected', [
                    'origin' => $request->header('Origin'),
                    'referer' => $request->header('Referer'),
                    'ip' => $request->ip()
                ]);
                return response()->json(['error' => 'Invalid origin'], 403);
            }

            // Check for required security headers
            if (!$this->hasRequiredHeaders($request)) {
                return response()->json(['error' => 'Missing security headers'], 400);
            }

            // Rate limiting for API calls
            if ($this->isRateLimited($request)) {
                return response()->json([
                    'error' => 'Rate limit exceeded',
                    'retry_after' => 60
                ], 429);
            }

            // Input validation and sanitization
            $this->sanitizeRequest($request);

            // Log suspicious activity
            $this->detectSuspiciousActivity($request);
        }

        $response = $next($request);

        // Add security headers to response
        if ($this->isApiRequest($request)) {
            $this->addSecurityHeaders($response, $request);
        }

        return $response;
    }

    /**
     * Check if request is API/AJAX
     */
    private function isApiRequest(Request $request): bool
    {
        return $request->ajax() || 
               $request->wantsJson() ||
               $request->is('api/*') ||
               $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Validate request origin
     */
    private function isValidOrigin(Request $request): bool
    {
        $allowedOrigins = [
            config('app.url'),
            'http://localhost:8000',
            'https://localhost:8000',
            // Add your production domains here
        ];

        $origin = $request->header('Origin');
        $referer = $request->header('Referer');

        // If no origin/referer, check if it's from same domain
        if (!$origin && !$referer) {
            return true; // Allow same-origin requests
        }

        // Check origin
        if ($origin) {
            foreach ($allowedOrigins as $allowedOrigin) {
                if (str_starts_with($origin, $allowedOrigin)) {
                    return true;
                }
            }
        }

        // Check referer as fallback
        if ($referer) {
            foreach ($allowedOrigins as $allowedOrigin) {
                if (str_starts_with($referer, $allowedOrigin)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check for required security headers
     */
    private function hasRequiredHeaders(Request $request): bool
    {
        $requiredHeaders = [
            'X-Requested-With',
        ];

        foreach ($requiredHeaders as $header) {
            if (!$request->hasHeader($header)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Rate limiting for API requests
     */
    private function isRateLimited(Request $request): bool
    {
        $key = 'api_rate_limit:' . $request->ip();
        $maxAttempts = 200; // 200 requests per minute for API
        $decayMinutes = 1;

        $attempts = Cache::get($key, 0);
        
        if ($attempts >= $maxAttempts) {
            return true;
        }

        Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));
        return false;
    }

    /**
     * Sanitize request input
     */
    private function sanitizeRequest(Request $request): void
    {
        $input = $request->all();
        
        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                // Remove potentially dangerous content
                $value = strip_tags($value);
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                
                // Remove javascript: protocol
                $value = preg_replace('/javascript:/i', '', $value);
                
                // Remove event handlers
                $value = preg_replace('/on\w+\s*=/i', '', $value);
            }
        });

        $request->merge($input);
    }

    /**
     * Detect suspicious activity
     */
    private function detectSuspiciousActivity(Request $request): void
    {
        $suspiciousPatterns = [
            'script', 'javascript:', 'eval(', 'expression(',
            'onload=', 'onerror=', 'onclick=', '<iframe',
            'document.cookie', 'document.write'
        ];

        $requestData = json_encode($request->all());
        
        foreach ($suspiciousPatterns as $pattern) {
            if (stripos($requestData, $pattern) !== false) {
                Log::warning('Suspicious pattern detected in API request', [
                    'pattern' => $pattern,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'data' => $requestData
                ]);
                break;
            }
        }

        // Check for SQL injection patterns
        $sqlPatterns = [
            'union select', 'drop table', 'insert into',
            'delete from', 'update set', '--', '/*'
        ];

        foreach ($sqlPatterns as $pattern) {
            if (stripos($requestData, $pattern) !== false) {
                Log::alert('Potential SQL injection attempt', [
                    'pattern' => $pattern,
                    'ip' => $request->ip(),
                    'data' => $requestData
                ]);
                break;
            }
        }
    }

    /**
     * Add security headers to API response
     */
    private function addSecurityHeaders(Response $response, Request $request): void
    {
        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        foreach ($headers as $name => $value) {
            $response->headers->set($name, $value);
        }

        // Add CORS headers for allowed origins
        $origin = $request->header('Origin');
        if ($origin && $this->isValidOrigin($request)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
        }
    }
}