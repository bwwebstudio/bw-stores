<?php

namespace App\Middleware;

use App\Core\Application;
use App\Core\Request;
use App\Core\Response;

/**
 * Rate Limit Middleware
 * 
 * File-based rate limiting to protect against brute force attacks.
 * Applied to login, registration, and password reset routes.
 */
class RateLimitMiddleware
{
    private int $maxAttempts;
    private int $decayMinutes;

    public function __construct(int $maxAttempts = 0, int $decayMinutes = 0)
    {
        // Use env config or defaults
        $this->maxAttempts = $maxAttempts ?: (int) ($_ENV['RATE_LIMIT_MAX_ATTEMPTS'] ?? 5);
        $this->decayMinutes = $decayMinutes ?: (int) ($_ENV['RATE_LIMIT_DECAY_MINUTES'] ?? 15);
    }

    /**
     * Handle the request.
     */
    public function handle(Request $request, Application $app): bool
    {
        // Only rate-limit POST requests (login attempts, form submissions)
        if ($request->method() !== 'POST') {
            return true;
        }

        $key = $this->resolveKey($request);
        $cacheDir = BASE_PATH . '/storage/cache/rate_limits';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $file = $cacheDir . '/' . md5($key) . '.json';

        // Load existing rate limit data
        $data = $this->loadData($file);

        // Clean expired entries
        $data = $this->cleanExpired($data);

        // Check if rate limit exceeded
        if (count($data['attempts']) >= $this->maxAttempts) {
            $oldestAttempt = min($data['attempts']);
            $retryAfter = $oldestAttempt + ($this->decayMinutes * 60) - time();

            app_log(
                "Rate limit exceeded for key: {$key} (IP: {$request->ip()}, URI: {$request->uri()})",
                'WARNING',
                'security.log'
            );

            if ($request->isAjax() || $request->expectsJson()) {
                Response::json([
                    'error' => 'Too many attempts. Please try again later.',
                    'retry_after' => max(0, $retryAfter),
                ], 429);
            } else {
                Response::tooManyRequests();
            }
            return false;
        }

        // Record this attempt
        $data['attempts'][] = time();
        $this->saveData($file, $data);

        return true;
    }

    /**
     * Record a failed attempt (call from controller on auth failure).
     */
    public static function recordFailedAttempt(Request $request): void
    {
        // This is handled automatically by the middleware on next request.
        // The middleware records every POST attempt.
    }

    /**
     * Clear rate limit for a key (call after successful login).
     */
    public static function clearAttempts(Request $request): void
    {
        $key = (new self())->resolveKey($request);
        $file = BASE_PATH . '/storage/cache/rate_limits/' . md5($key) . '.json';

        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Generate a rate limit key based on IP + URI.
     */
    private function resolveKey(Request $request): string
    {
        return $request->ip() . '|' . $request->uri();
    }

    /**
     * Load rate limit data from file.
     */
    private function loadData(string $file): array
    {
        if (!file_exists($file)) {
            return ['attempts' => []];
        }

        $content = file_get_contents($file);
        $data = json_decode($content, true);

        return $data ?: ['attempts' => []];
    }

    /**
     * Save rate limit data to file.
     */
    private function saveData(string $file, array $data): void
    {
        file_put_contents($file, json_encode($data), LOCK_EX);
    }

    /**
     * Remove expired entries from the data.
     */
    private function cleanExpired(array $data): array
    {
        $cutoff = time() - ($this->decayMinutes * 60);

        $data['attempts'] = array_values(array_filter(
            $data['attempts'],
            fn($timestamp) => $timestamp > $cutoff
        ));

        return $data;
    }
}
