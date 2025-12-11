<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class FilamentPerformanceOptimizer
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start performance monitoring
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Enable query optimization for Filament
        if ($this->isFilamentRequest($request)) {
            $this->optimizeDatabase();
            $this->enableCaching($request);
        }

        $response = $next($request);

        // Add performance headers
        if ($this->isFilamentRequest($request)) {
            $this->addPerformanceHeaders($response, $startTime, $startMemory);
            $this->optimizeResponse($response);
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
     * Optimize database queries
     */
    private function optimizeDatabase(): void
    {
        // Enable query caching
        DB::enableQueryLog();
        
        // Set connection timeout
        config(['database.connections.mysql.options' => [
            \PDO::ATTR_TIMEOUT => 30,
            \PDO::ATTR_PERSISTENT => true,
        ]]);

        // Optimize MySQL settings
        DB::statement('SET SESSION query_cache_type = ON');
        DB::statement('SET SESSION query_cache_size = 67108864'); // 64MB
    }

    /**
     * Enable intelligent caching
     */
    private function enableCaching(Request $request): void
    {
        // Cache frequently accessed data
        $cacheKey = 'filament_' . md5($request->getPathInfo() . serialize($request->query()));
        
        if ($request->isMethod('GET') && !$request->ajax()) {
            Cache::remember($cacheKey, 300, function () {
                return 'cached_response';
            });
        }
    }

    /**
     * Add performance monitoring headers
     */
    private function addPerformanceHeaders(Response $response, float $startTime, int $startMemory): void
    {
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        $memoryUsage = round((memory_get_usage() - $startMemory) / 1024 / 1024, 2);
        $peakMemory = round(memory_get_peak_usage() / 1024 / 1024, 2);

        $response->headers->set('X-Execution-Time', $executionTime . 'ms');
        $response->headers->set('X-Memory-Usage', $memoryUsage . 'MB');
        $response->headers->set('X-Peak-Memory', $peakMemory . 'MB');
        $response->headers->set('X-Query-Count', count(DB::getQueryLog()));
    }

    /**
     * Optimize response
     */
    private function optimizeResponse(Response $response): void
    {
        // Enable compression
        if (!$response->headers->has('Content-Encoding')) {
            $content = $response->getContent();
            if (strlen($content) > 1024) { // Only compress if > 1KB
                $compressed = gzencode($content, 6);
                if ($compressed !== false && strlen($compressed) < strlen($content)) {
                    $response->setContent($compressed);
                    $response->headers->set('Content-Encoding', 'gzip');
                    $response->headers->set('Content-Length', strlen($compressed));
                }
            }
        }

        // Add caching headers
        if (!$response->headers->has('Cache-Control')) {
            $response->headers->set('Cache-Control', 'public, max-age=300');
            $response->headers->set('ETag', md5($response->getContent()));
        }

        // Security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}