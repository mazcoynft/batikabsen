<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class FilamentSecurityEnhancer
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Apply security measures for Filament admin
        if ($this->isFilamentRequest($request)) {
            // Rate limiting
            if ($this->isRateLimited($request)) {
                return $this->rateLimitResponse();
            }

            // IP whitelist check (optional)
            if (!$this->isAllowedIP($request)) {
                Log::warning('Unauthorized IP access attempt', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'path' => $request->path()
                ]);
                abort(403, 'Access denied');
            }

            // Suspicious activity detection
            $this->detectSuspiciousActivity($request);

            // Session security
            $this->enhanceSessionSecurity($request);
        }

        $response = $next($request);

        // Add security headers
        if ($this->isFilamentRequest($request)) {
            $this->addSecurityHeaders($response);
        }

        return $response;
    }

    /**
     * Check if request is for Filament
     */
    private function isFilamentRequest(Request $request): bool
    {
        return $request->is('admin/*') || 
               $request->is('filament/*') ||
               str_contains($request->path(), 'admin');
    }

    /**
     * Check rate limiting
     */
    private function isRateLimited(Request $request): bool
    {
        $key = 'filament_rate_limit:' . $request->ip();
        
        // Different limits for different actions
        if ($request->is('admin/login')) {
            return RateLimiter::tooManyAttempts($key . ':login', 5); // 5 attempts per minute
        }

        if ($request->isMethod('POST')) {
            return RateLimiter::tooManyAttempts($key . ':post', 30); // 30 POST requests per minute
        }

        return RateLimiter::tooManyAttempts($key . ':general', 100); // 100 requests per minute
    }

    /**
     * Rate limit response
     */
    private function rateLimitResponse(): Response
    {
        return response()->json([
            'message' => 'Too many requests. Please try again later.',
            'retry_after' => 60
        ], 429);
    }

    /**
     * Check if IP is allowed (implement your whitelist logic)
     */
    private function isAllowedIP(Request $request): bool
    {
        // Get allowed IPs from config or database
        $allowedIPs = config('filament.security.allowed_ips', []);
        
        // If no whitelist configured, allow all
        if (empty($allowedIPs)) {
            return true;
        }

        $clientIP = $request->ip();
        
        foreach ($allowedIPs as $allowedIP) {
            if ($this->ipMatches($clientIP, $allowedIP)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP matches pattern (supports CIDR)
     */
    private function ipMatches(string $ip, string $pattern): bool
    {
        if ($ip === $pattern) {
            return true;
        }

        // Support CIDR notation
        if (strpos($pattern, '/') !== false) {
            list($subnet, $mask) = explode('/', $pattern);
            return (ip2long($ip) & ~((1 << (32 - $mask)) - 1)) === ip2long($subnet);
        }

        return false;
    }

    /**
     * Detect suspicious activity
     */
    private function detectSuspiciousActivity(Request $request): void
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        
        // Check for suspicious patterns
        $suspiciousPatterns = [
            'bot', 'crawler', 'spider', 'scraper',
            'hack', 'exploit', 'injection', 'script'
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                Log::warning('Suspicious user agent detected', [
                    'ip' => $ip,
                    'user_agent' => $userAgent,
                    'pattern' => $pattern
                ]);
                break;
            }
        }

        // Track failed login attempts
        if ($request->is('admin/login') && $request->isMethod('POST')) {
            $key = 'failed_logins:' . $ip;
            $attempts = Cache::get($key, 0);
            
            if ($attempts > 10) { // More than 10 failed attempts
                Log::alert('Multiple failed login attempts', [
                    'ip' => $ip,
                    'attempts' => $attempts
                ]);
            }
        }
    }

    /**
     * Enhance session security
     */
    private function enhanceSessionSecurity(Request $request): void
    {
        // Only proceed if session is available
        if (!$request->hasSession()) {
            return;
        }
        
        // Regenerate session ID periodically
        if (!$request->session()->has('last_regeneration')) {
            $request->session()->put('last_regeneration', time());
            $request->session()->regenerate();
        } else {
            $lastRegeneration = $request->session()->get('last_regeneration');
            if ((time() - $lastRegeneration) > 1800) { // 30 minutes
                $request->session()->put('last_regeneration', time());
                $request->session()->regenerate();
            }
        }

        // Store security fingerprint
        $fingerprint = md5(
            $request->ip() . 
            $request->userAgent() . 
            $request->header('Accept-Language', '')
        );

        if ($request->session()->has('security_fingerprint')) {
            if ($request->session()->get('security_fingerprint') !== $fingerprint) {
                Log::warning('Session fingerprint mismatch', [
                    'ip' => $request->ip(),
                    'expected' => $request->session()->get('security_fingerprint'),
                    'actual' => $fingerprint
                ]);
                
                // Optionally invalidate session
                // $request->session()->invalidate();
            }
        } else {
            $request->session()->put('security_fingerprint', $fingerprint);
        }
    }

    /**
     * Add comprehensive security headers
     */
    private function addSecurityHeaders(Response $response): void
    {
        $headers = [
            // Prevent XSS
            'X-XSS-Protection' => '1; mode=block',
            
            // Prevent MIME type sniffing
            'X-Content-Type-Options' => 'nosniff',
            
            // Prevent clickjacking
            'X-Frame-Options' => 'SAMEORIGIN',
            
            // Referrer policy
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            
            // Content Security Policy
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com; font-src 'self' fonts.gstatic.com; img-src 'self' data: blob:; connect-src 'self';",
            
            // Strict Transport Security (HTTPS only)
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            
            // Permissions Policy
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
            
            // Remove server information
            'Server' => 'UssiBatik-Server'
        ];

        foreach ($headers as $name => $value) {
            $response->headers->set($name, $value);
        }

        // Remove potentially sensitive headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
    }
}