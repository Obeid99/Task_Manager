<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

/**
 * Service to monitor and log performance metrics
 */
class PerformanceMonitorService
{
    private LoggerInterface $logger;
    private array $timers = [];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Start timing an operation
     */
    public function startTimer(string $operation): void
    {
        $this->timers[$operation] = microtime(true);
    }

    /**
     * End timing an operation and log if it's slow
     */
    public function endTimer(string $operation, float $slowThreshold = 1.0): float
    {
        if (!isset($this->timers[$operation])) {
            return 0.0;
        }

        $duration = microtime(true) - $this->timers[$operation];
        unset($this->timers[$operation]);

        if ($duration > $slowThreshold) {
            $this->logger->warning('Slow operation detected', [
                'operation' => $operation,
                'duration' => $duration,
                'threshold' => $slowThreshold
            ]);
        }

        return $duration;
    }

    /**
     * Log memory usage
     */
    public function logMemoryUsage(string $context = ''): void
    {
        $memoryUsage = memory_get_usage(true);
        $peakMemory = memory_get_peak_usage(true);

        $this->logger->info('Memory usage', [
            'context' => $context,
            'current_memory' => $this->formatBytes($memoryUsage),
            'peak_memory' => $this->formatBytes($peakMemory)
        ]);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Profile a callable and return its result
     */
    public function profile(callable $callable, string $operation, float $slowThreshold = 1.0)
    {
        $this->startTimer($operation);
        $result = $callable();
        $this->endTimer($operation, $slowThreshold);
        
        return $result;
    }
}
