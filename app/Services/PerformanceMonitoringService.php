<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceMonitoringService
{
    /**
     * Monitor application performance
     */
    public static function monitor(): array
    {
        $metrics = [
            'response_time' => self::getAverageResponseTime(),
            'database_performance' => self::getDatabasePerformance(),
            'cache_hit_rate' => self::getCacheHitRate(),
            'memory_usage' => self::getMemoryUsage(),
            'security_events' => self::getSecurityEvents(),
        ];

        // Store metrics for trending
        self::storeMetrics($metrics);

        return $metrics;
    }

    /**
     * Get average response time
     */
    private static function getAverageResponseTime(): float
    {
        $key = 'performance_response_times';
        $times = Cache::get($key, []);
        
        if (empty($times)) {
            return 0.0;
        }

        return array_sum($times) / count($times);
    }

    /**
     * Record response time
     */
    public static function recordResponseTime(float $time): void
    {
        $key = 'performance_response_times';
        $times = Cache::get($key, []);
        
        // Keep only last 100 measurements
        if (count($times) >= 100) {
            array_shift($times);
        }
        
        $times[] = $time;
        Cache::put($key, $times, 3600); // 1 hour
    }

    /**
     * Get database performance metrics
     */
    private static function getDatabasePerformance(): array
    {
        $queries = DB::getQueryLog();
        
        if (empty($queries)) {
            return [
                'query_count' => 0,
                'average_time' => 0,
                'slow_queries' => 0
            ];
        }

        $totalTime = array_sum(array_column($queries, 'time'));
        $slowQueries = array_filter($queries, fn($q) => $q['time'] > 1000); // > 1 second

        return [
            'query_count' => count($queries),
            'average_time' => $totalTime / count($queries),
            'slow_queries' => count($slowQueries)
        ];
    }

    /**
     * Get cache hit rate
     */
    private static function getCacheHitRate(): float
    {
        $hits = Cache::get('cache_hits', 0);
        $misses = Cache::get('cache_misses', 0);
        $total = $hits + $misses;

        return $total > 0 ? ($hits / $total) * 100 : 0;
    }

    /**
     * Record cache hit
     */
    public static function recordCacheHit(): void
    {
        Cache::increment('cache_hits');
    }

    /**
     * Record cache miss
     */
    public static function recordCacheMiss(): void
    {
        Cache::increment('cache_misses');
    }

    /**
     * Get memory usage
     */
    private static function getMemoryUsage(): array
    {
        return [
            'current' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
            'limit' => ini_get('memory_limit')
        ];
    }

    /**
     * Get security events
     */
    private static function getSecurityEvents(): array
    {
        $events = Cache::get('security_events', []);
        
        return [
            'total_events' => count($events),
            'recent_events' => array_slice($events, -10), // Last 10 events
            'event_types' => array_count_values(array_column($events, 'type'))
        ];
    }

    /**
     * Record security event
     */
    public static function recordSecurityEvent(string $type, array $data): void
    {
        $events = Cache::get('security_events', []);
        
        // Keep only last 1000 events
        if (count($events) >= 1000) {
            array_shift($events);
        }
        
        $events[] = [
            'type' => $type,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ];
        
        Cache::put('security_events', $events, 86400); // 24 hours
    }

    /**
     * Store performance metrics
     */
    private static function storeMetrics(array $metrics): void
    {
        $key = 'performance_history_' . now()->format('Y-m-d-H');
        $history = Cache::get($key, []);
        
        $history[] = [
            'timestamp' => now()->toISOString(),
            'metrics' => $metrics
        ];
        
        // Keep hourly data for 24 hours
        Cache::put($key, $history, 86400);
    }

    /**
     * Get performance trends
     */
    public static function getTrends(): array
    {
        $trends = [];
        
        // Get last 24 hours of data
        for ($i = 0; $i < 24; $i++) {
            $hour = now()->subHours($i)->format('Y-m-d-H');
            $key = 'performance_history_' . $hour;
            $data = Cache::get($key, []);
            
            if (!empty($data)) {
                $trends[$hour] = $data;
            }
        }
        
        return $trends;
    }

    /**
     * Check if performance is healthy
     */
    public static function isHealthy(): bool
    {
        $metrics = self::monitor();
        
        // Define health thresholds
        $thresholds = [
            'response_time' => 2000, // 2 seconds
            'memory_usage_percent' => 80, // 80%
            'cache_hit_rate' => 70, // 70%
        ];
        
        // Check response time
        if ($metrics['response_time'] > $thresholds['response_time']) {
            return false;
        }
        
        // Check memory usage
        $memoryPercent = ($metrics['memory_usage']['current'] / self::parseMemoryLimit($metrics['memory_usage']['limit'])) * 100;
        if ($memoryPercent > $thresholds['memory_usage_percent']) {
            return false;
        }
        
        // Check cache hit rate
        if ($metrics['cache_hit_rate'] < $thresholds['cache_hit_rate']) {
            return false;
        }
        
        return true;
    }

    /**
     * Parse memory limit string to bytes
     */
    private static function parseMemoryLimit(string $limit): int
    {
        $limit = trim($limit);
        $last = strtolower($limit[strlen($limit) - 1]);
        $value = (int) $limit;
        
        switch ($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }

    /**
     * Generate performance report
     */
    public static function generateReport(): array
    {
        return [
            'current_metrics' => self::monitor(),
            'trends' => self::getTrends(),
            'health_status' => self::isHealthy(),
            'recommendations' => self::getRecommendations()
        ];
    }

    /**
     * Get performance recommendations
     */
    private static function getRecommendations(): array
    {
        $recommendations = [];
        $metrics = self::monitor();
        
        if ($metrics['response_time'] > 1000) {
            $recommendations[] = 'Consider optimizing database queries or adding more caching';
        }
        
        if ($metrics['cache_hit_rate'] < 80) {
            $recommendations[] = 'Improve cache strategy to increase hit rate';
        }
        
        if ($metrics['database_performance']['slow_queries'] > 0) {
            $recommendations[] = 'Optimize slow database queries';
        }
        
        return $recommendations;
    }
}