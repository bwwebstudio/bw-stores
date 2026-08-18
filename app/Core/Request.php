<?php

namespace App\Core;

/**
 * Request
 * 
 * Wraps PHP superglobals into a clean, safe request object.
 * Provides input sanitization and retrieval helpers.
 */
class Request
{
    private array $get;
    private array $post;
    private array $server;
    private array $files;
    private array $cookies;
    private array $routeParams = [];
    private ?array $jsonBody = null;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->files = $_FILES;
        $this->cookies = $_COOKIE;
    }

    /**
     * Get the HTTP method (GET, POST, PUT, PATCH, DELETE).
     */
    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Get the request URI (path only, no query string).
     */
    public function uri(): string
    {
        $uri = parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $uri = '/' . trim($uri, '/');
        
        // Normalize: keep / as /, remove trailing slash from everything else
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        return $uri;
    }

    /**
     * Get the full URL including query string.
     */
    public function fullUrl(): string
    {
        $scheme = $this->isSecure() ? 'https' : 'http';
        $host = $this->server['HTTP_HOST'] ?? 'localhost';
        $uri = $this->server['REQUEST_URI'] ?? '/';
        return $scheme . '://' . $host . $uri;
    }

    /**
     * Check if request is HTTPS.
     */
    public function isSecure(): bool
    {
        return (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off')
            || ($this->server['SERVER_PORT'] ?? 80) == 443
            || ($this->server['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    }

    /**
     * Get an input value from GET, POST, or JSON body.
     */
    public function input(string $key, mixed $default = null): mixed
    {
        // Check POST first
        if (isset($this->post[$key])) {
            return $this->post[$key];
        }

        // Check GET
        if (isset($this->get[$key])) {
            return $this->get[$key];
        }

        // Check JSON body
        $json = $this->json();
        if (isset($json[$key])) {
            return $json[$key];
        }

        return $default;
    }

    /**
     * Get all input data (merged GET + POST + JSON).
     */
    public function all(): array
    {
        return array_merge($this->get, $this->post, $this->json() ?? []);
    }

    /**
     * Get only specified keys from input.
     */
    public function only(array $keys): array
    {
        $all = $this->all();
        return array_intersect_key($all, array_flip($keys));
    }

    /**
     * Get a query string parameter.
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    /**
     * Get a POST parameter.
     */
    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Get the JSON body as an array.
     */
    public function json(): array
    {
        if ($this->jsonBody === null) {
            $contentType = $this->server['CONTENT_TYPE'] ?? '';
            if (str_contains($contentType, 'application/json')) {
                $body = file_get_contents('php://input');
                $this->jsonBody = json_decode($body, true) ?? [];
            } else {
                $this->jsonBody = [];
            }
        }
        return $this->jsonBody;
    }

    /**
     * Check if the request expects JSON response.
     */
    public function expectsJson(): bool
    {
        $accept = $this->server['HTTP_ACCEPT'] ?? '';
        return str_contains($accept, 'application/json');
    }

    /**
     * Check if the request is an AJAX/XHR request.
     */
    public function isAjax(): bool
    {
        return ($this->server['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest'
            || $this->expectsJson();
    }

    /**
     * Get an uploaded file.
     */
    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Check if a file was uploaded.
     */
    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    /**
     * Get a server variable.
     */
    public function server(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }

    /**
     * Get a cookie value.
     */
    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->cookies[$key] ?? $default;
    }

    /**
     * Get the client IP address.
     */
    public function ip(): string
    {
        // Check for forwarded IP (behind proxy/load balancer)
        if (!empty($this->server['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $this->server['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }

        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Get the User Agent string.
     */
    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Set route parameters (called by Router after matching).
     */
    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    /**
     * Get a route parameter by index.
     */
    public function routeParam(int $index, mixed $default = null): mixed
    {
        return $this->routeParams[$index] ?? $default;
    }

    /**
     * Get all route parameters.
     */
    public function routeParams(): array
    {
        return $this->routeParams;
    }

    /**
     * Check if a given input key has a value.
     */
    public function has(string $key): bool
    {
        $value = $this->input($key);
        return $value !== null && $value !== '';
    }
}
