<?php

namespace App\Core;

/**
 * Application
 * 
 * Central application class. Boots services, runs middleware,
 * and dispatches requests through the router.
 */
class Application
{
    private static ?Application $instance = null;
    private array $config;
    private Router $router;
    private Request $request;
    private Session $session;
    private ?Database $database = null;

    public function __construct(array $config)
    {
        $this->config = $config;
        self::$instance = $this;
    }

    /**
     * Get the singleton application instance.
     */
    public static function getInstance(): ?Application
    {
        return self::$instance;
    }

    /**
     * Boot the application: start session, initialize services.
     */
    public function boot(): void
    {
        // Create storage directories if they don't exist
        $this->ensureDirectories();

        // Initialize request
        $this->request = new Request();

        // Initialize session
        $this->session = new Session($this->config['session']);
        $this->session->start();

        // Initialize database
        $this->getDatabase();

        // Initialize router
        $this->router = new Router($this);
    }

    /**
     * Dispatch the current request through the router.
     */
    public function dispatch(): void
    {
        $this->router->dispatch($this->request);
    }

    /**
     * Get a configuration value using dot notation.
     * Example: config('app.name'), config('database.host')
     */
    public function config(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Get the full config array.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get the router instance.
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Get the request instance.
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Get the session instance.
     */
    public function getSession(): Session
    {
        return $this->session;
    }

    /**
     * Get the database instance (lazy loaded).
     */
    public function getDatabase(): Database
    {
        if ($this->database === null) {
            $this->database = new Database($this->config['database']);
        }
        return $this->database;
    }

    /**
     * Ensure required directories exist.
     */
    private function ensureDirectories(): void
    {
        $dirs = [
            BASE_PATH . '/storage/logs',
            BASE_PATH . '/storage/cache',
            BASE_PATH . '/storage/cache/rate_limits',
            BASE_PATH . '/public/uploads',
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
}
