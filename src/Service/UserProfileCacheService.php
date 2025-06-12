<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Service to handle caching of user profile completion calculations
 * This prevents repeated database queries for profile completion percentages
 */
class UserProfileCacheService
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $userProfileCache)
    {
        $this->cache = $userProfileCache;
    }

    /**
     * Get cached profile completion percentage or calculate and cache it
     */
    public function getProfileCompletionPercentage(User $user): int
    {
        $cacheKey = 'user_profile_completion_' . $user->getId();
        
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($user) {
            // Cache for 1 hour
            $item->expiresAfter(3600);
            
            // Calculate profile completion
            return $this->calculateProfileCompletion($user);
        });
    }

    /**
     * Clear cached profile completion for a user
     */
    public function clearProfileCompletionCache(User $user): void
    {
        $cacheKey = 'user_profile_completion_' . $user->getId();
        $this->cache->delete($cacheKey);
    }

    /**
     * Clear cached profile completion for multiple users
     */
    public function clearMultipleProfileCompletionCache(array $userIds): void
    {
        foreach ($userIds as $userId) {
            $cacheKey = 'user_profile_completion_' . $userId;
            $this->cache->delete($cacheKey);
        }
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion(User $user): int
    {
        $score = 0;
        $maxScore = 8;

        // Basic info (4 points)
        if ($user->getFirstName()) $score++;
        if ($user->getLastName()) $score++;
        if ($user->getJobTitle()) $score++;
        if ($user->getEmail()) $score++; // Always present

        // Profile sections (4 points)
        if (!$user->getEducations()->isEmpty()) $score++;
        if (!$user->getWorkExperiences()->isEmpty()) $score++;
        if (!$user->getSkills()->isEmpty()) $score++;
        if ($user->getCvUpload()) $score++;

        return (int) round(($score / $maxScore) * 100);
    }

    /**
     * Warm up cache for multiple users (useful for admin pages)
     */
    public function warmUpProfileCompletionCache(array $users): void
    {
        foreach ($users as $user) {
            $this->getProfileCompletionPercentage($user);
        }
    }

    /**
     * Get profile completion statistics for multiple users
     */
    public function getMultipleProfileCompletionPercentages(array $users): array
    {
        $results = [];
        foreach ($users as $user) {
            $results[$user->getId()] = $this->getProfileCompletionPercentage($user);
        }
        return $results;
    }
}
