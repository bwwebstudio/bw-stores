<?php

namespace App\Core;

/**
 * Response
 * 
 * HTTP response builder for JSON, redirects, and views.
 */
class Response
{
    /**
     * Send a JSON response.
     */
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect to a URL.
     */
    public static function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header('Location: ' . $url);
        exit;
    }

    /**
     * Redirect back to the previous page.
     */
    public static function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        self::redirect($referer);
    }

    /**
     * Send an error response.
     */
    public static function error(int $status, string $message = ''): void
    {
        http_response_code($status);

        $errorView = BASE_PATH . '/resources/views/errors/' . $status . '.php';
        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo "<h1>Error {$status}</h1>";
            if ($message) {
                echo '<p>' . htmlspecialchars($message) . '</p>';
            }
        }
        exit;
    }

    /**
     * Send a 403 Forbidden response.
     */
    public static function forbidden(): void
    {
        self::error(403);
    }

    /**
     * Send a 404 Not Found response.
     */
    public static function notFound(): void
    {
        self::error(404);
    }

    /**
     * Send a 419 CSRF Token Expired response.
     */
    public static function csrfExpired(): void
    {
        self::error(419);
    }

    /**
     * Send a 429 Too Many Requests response.
     */
    public static function tooManyRequests(): void
    {
        self::error(429);
    }

    /**
     * Set a response header.
     */
    public static function header(string $name, string $value): void
    {
        header($name . ': ' . $value);
    }

    /**
     * Send no content (204).
     */
    public static function noContent(): void
    {
        http_response_code(204);
        exit;
    }
}
