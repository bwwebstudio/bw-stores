<?php

/**
 * Global Helper Functions
 * 
 * Utility functions available throughout the application.
 * Auto-loaded via Composer.
 */

use App\Core\Application;
use App\Core\Response;
use App\Core\View;

/**
 * Get an environment variable with fallback to getenv().
 */
function env(string $key, mixed $default = null): mixed
{
    $val = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    if ($val === false || $val === null || $val === '') {
        return $default;
    }
    $lower = strtolower(trim((string)$val));
    if ($lower === 'true' || $lower === '(true)') return true;
    if ($lower === 'false' || $lower === '(false)') return false;
    if ($lower === 'empty' || $lower === '(empty)') return '';
    if ($lower === 'null' || $lower === '(null)') return null;

    return $val;
}

/**
 * Get a configuration value.
 */
function config(string $key, mixed $default = null): mixed
{
    $app = Application::getInstance();
    return $app ? $app->config($key, $default) : $default;
}

/**
 * Get the session instance.
 */
function session(): App\Core\Session
{
    return Application::getInstance()->getSession();
}

/**
 * Get the database instance.
 */
function db(): App\Core\Database
{
    return Application::getInstance()->getDatabase();
}

/**
 * Generate a full URL for a given path.
 * Dynamically uses the current request host if available to avoid origin mismatches.
 */
function url(string $path = ''): string
{
    if (!empty($_SERVER['HTTP_HOST'])) {
        $scheme = 'http';
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')) {
            $scheme = 'https';
        }
        $baseUrl = $scheme . '://' . $_SERVER['HTTP_HOST'];
    } else {
        $baseUrl = rtrim(config('app.url', 'http://localhost:8000'), '/');
    }

    if ($path === '' || $path === '/') {
        return $baseUrl;
    }
    return $baseUrl . '/' . ltrim($path, '/');
}

/**
 * Generate a URL for a public asset.
 */
function asset(string $path): string
{
    return url('public/assets/' . ltrim($path, '/'));
}

/**
 * Render a view.
 */
function view(string $view, array $data = []): void
{
    View::render($view, $data);
}

/**
 * Redirect to a URL.
 */
function redirect(string $url): void
{
    Response::redirect($url);
}

/**
 * Redirect back to previous page.
 */
function back(): void
{
    Response::back();
}

/**
 * Send a JSON response.
 */
function json_response(array $data, int $status = 200): void
{
    Response::json($data, $status);
}

/**
 * Get old input value (for form repopulation after validation errors).
 */
function old(string $key, mixed $default = ''): mixed
{
    return session()->oldInput($key, $default);
}

/**
 * Set a flash message.
 */
function flash(string $key, mixed $value): void
{
    session()->flash($key, $value);
}

/**
 * Get a flash message.
 */
function get_flash(string $key, mixed $default = null): mixed
{
    return session()->getFlash($key, $default);
}

/**
 * Check if flash message exists.
 */
function has_flash(string $key): bool
{
    return session()->hasFlash($key);
}

/**
 * Get validation errors.
 */
function errors(): array
{
    return session()->getErrors();
}

/**
 * Get a specific field error.
 */
function error(string $field): string
{
    $errors = errors();
    return $errors[$field] ?? '';
}

/**
 * Check if a field has an error.
 */
function has_error(string $field): bool
{
    $errors = errors();
    return isset($errors[$field]);
}

/**
 * Debug dump (development only).
 */
function dd(mixed ...$vars): void
{
    if (!config('app.debug', false)) {
        return;
    }

    echo '<pre style="background:#1e1e2e;color:#cdd6f4;padding:16px;border-radius:8px;font-family:monospace;font-size:13px;overflow-x:auto;">';
    foreach ($vars as $var) {
        var_dump($var);
        echo "\n";
    }
    echo '</pre>';
    exit;
}

/**
 * Log a message to the application log.
 */
function app_log(string $message, string $level = 'INFO', string $file = 'app.log'): void
{
    $logDir = BASE_PATH . '/storage/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $logEntry = sprintf(
        "[%s] [%s] %s\n",
        date('Y-m-d H:i:s'),
        strtoupper($level),
        $message
    );

    error_log($logEntry, 3, $logDir . '/' . $file);
}

/**
 * Format a price in INR.
 */
function format_price(float|int $amount, string $currency = 'INR'): string
{
    if ($currency === 'INR') {
        return '₹' . number_format($amount, 2);
    }
    return $currency . ' ' . number_format($amount, 2);
}

/**
 * Generate a slug from a string.
 */
function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text ?: 'n-a';
}

/**
 * Truncate a string to a specified length.
 */
function str_truncate(string $string, int $length = 100, string $append = '...'): string
{
    if (mb_strlen($string) <= $length) {
        return $string;
    }
    return mb_substr($string, 0, $length) . $append;
}

/**
 * Generate a human-readable time difference.
 */
function time_ago(string $datetime): string
{
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    if ($diff < 2592000) return floor($diff / 604800) . ' weeks ago';

    return date('M j, Y', $time);
}

/**
 * Check if the current URL matches a given path.
 * Useful for active nav states.
 */
function is_active(string $path): bool
{
    $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
    $currentPath = parse_url($currentUri, PHP_URL_PATH);
    
    // Exact match or starts with (for nested routes)
    return $currentPath === $path || str_starts_with($currentPath, $path . '/');
}

/**
 * Generate active class for navigation.
 */
function active_class(string $path, string $class = 'active'): string
{
    return is_active($path) ? $class : '';
}
