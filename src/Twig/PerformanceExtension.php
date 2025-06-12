<?php

namespace App\Twig;

use App\Service\UserProfileCacheService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension to provide performance-optimized functions
 */
class PerformanceExtension extends AbstractExtension
{
    private UserProfileCacheService $cacheService;

    public function __construct(UserProfileCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cached_profile_completion', [$this, 'getCachedProfileCompletion']),
            new TwigFunction('format_file_size', [$this, 'formatFileSize']),
            new TwigFunction('truncate_smart', [$this, 'truncateSmart']),
        ];
    }

    /**
     * Get cached profile completion percentage
     */
    public function getCachedProfileCompletion($user): int
    {
        return $this->cacheService->getProfileCompletionPercentage($user);
    }

    /**
     * Format file size in human readable format
     */
    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Smart truncation that preserves word boundaries
     */
    public function truncateSmart(string $text, int $length = 50, string $suffix = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        $truncated = substr($text, 0, $length);
        $lastSpace = strrpos($truncated, ' ');
        
        if ($lastSpace !== false && $lastSpace > $length * 0.7) {
            $truncated = substr($truncated, 0, $lastSpace);
        }

        return $truncated . $suffix;
    }
}
