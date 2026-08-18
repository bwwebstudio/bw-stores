<?php

namespace App\Core;

/**
 * Router
 * 
 * Clean URL router supporting GET, POST, PUT, PATCH, DELETE.
 * Supports named routes, route parameters, and middleware groups.
 */
class Router
{
    private Application $app;
    private array $routes = [];
    private array $namedRoutes = [];
    private array $currentGroupMiddleware = [];
    private string $currentPrefix = '';

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register a GET route.
     */
    public function get(string $uri, string|array|callable $action, string $name = ''): self
    {
        return $this->addRoute('GET', $uri, $action, $name);
    }

    /**
     * Register a POST route.
     */
    public function post(string $uri, string|array|callable $action, string $name = ''): self
    {
        return $this->addRoute('POST', $uri, $action, $name);
    }

    /**
     * Register a PUT route.
     */
    public function put(string $uri, string|array|callable $action, string $name = ''): self
    {
        return $this->addRoute('PUT', $uri, $action, $name);
    }

    /**
     * Register a PATCH route.
     */
    public function patch(string $uri, string|array|callable $action, string $name = ''): self
    {
        return $this->addRoute('PATCH', $uri, $action, $name);
    }

    /**
     * Register a DELETE route.
     */
    public function delete(string $uri, string|array|callable $action, string $name = ''): self
    {
        return $this->addRoute('DELETE', $uri, $action, $name);
    }

    /**
     * Create a route group with shared middleware and/or prefix.
     */
    public function group(array $options, callable $callback): void
    {
        $previousMiddleware = $this->currentGroupMiddleware;
        $previousPrefix = $this->currentPrefix;

        // Merge middleware
        if (isset($options['middleware'])) {
            $middleware = is_array($options['middleware']) ? $options['middleware'] : [$options['middleware']];
            $this->currentGroupMiddleware = array_merge($this->currentGroupMiddleware, $middleware);
        }

        // Merge prefix
        if (isset($options['prefix'])) {
            $this->currentPrefix .= '/' . trim($options['prefix'], '/');
        }

        $callback($this);

        // Restore previous state
        $this->currentGroupMiddleware = $previousMiddleware;
        $this->currentPrefix = $previousPrefix;
    }

    /**
     * Dispatch the current request.
     */
    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri = $request->uri();

        // Support method spoofing via _method field for PUT/PATCH/DELETE
        if ($method === 'POST' && $request->input('_method')) {
            $spoofed = strtoupper($request->input('_method'));
            if (in_array($spoofed, ['PUT', 'PATCH', 'DELETE'])) {
                $method = $spoofed;
            }
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->matchRoute($route['pattern'], $uri);
            if ($params !== false) {
                // Run middleware
                foreach ($route['middleware'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $result = $middleware->handle($request, $this->app);
                    if ($result === false) {
                        return; // Middleware halted the request
                    }
                }

                // Store route params on request
                $request->setRouteParams($params);

                // Execute the action
                $this->executeAction($route['action'], $request, $params);
                return;
            }
        }

        // No route matched — 404
        $this->send404();
    }

    /**
     * Generate URL for a named route.
     */
    public function url(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \RuntimeException("Route '{$name}' not found.");
        }

        $uri = $this->namedRoutes[$name];

        foreach ($params as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value, $uri);
        }

        return $this->app->config('app.url') . $uri;
    }

    /**
     * Add a route to the registry.
     */
    private function addRoute(string $method, string $uri, string|array|callable $action, string $name): self
    {
        $fullUri = $this->currentPrefix . '/' . trim($uri, '/');
        $fullUri = '/' . trim($fullUri, '/');

        // Normalize: /dashboard/ → /dashboard, but keep / as /
        if ($fullUri !== '/') {
            $fullUri = rtrim($fullUri, '/');
        }

        $route = [
            'method'     => $method,
            'uri'        => $fullUri,
            'pattern'    => $this->uriToPattern($fullUri),
            'action'     => $action,
            'middleware'  => $this->currentGroupMiddleware,
        ];

        $this->routes[] = $route;

        if ($name) {
            $this->namedRoutes[$name] = $fullUri;
        }

        return $this;
    }

    /**
     * Convert a URI with parameters to a regex pattern.
     * Example: /store/{slug}/product/{id} → #^/store/([^/]+)/product/([^/]+)$#
     */
    private function uriToPattern(string $uri): string
    {
        $pattern = preg_replace('#\{([a-zA-Z_]+)\}#', '([^/]+)', $uri);
        return '#^' . $pattern . '$#';
    }

    /**
     * Match a URI against a route pattern.
     * Returns array of matched parameters or false.
     */
    private function matchRoute(string $pattern, string $uri): array|false
    {
        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // Remove full match
            return $matches;
        }
        return false;
    }

    /**
     * Execute a route action (controller method or callable).
     */
    private function executeAction(string|array|callable $action, Request $request, array $params): void
    {
        if (is_callable($action) && !is_string($action)) {
            call_user_func_array($action, array_merge([$request], $params));
            return;
        }

        // String format: "Controller@method"
        if (is_string($action) && str_contains($action, '@')) {
            $action = explode('@', $action);
        }

        if (is_array($action) && count($action) === 2) {
            [$controllerClass, $method] = $action;

            if (!class_exists($controllerClass)) {
                throw new \RuntimeException("Controller class '{$controllerClass}' not found.");
            }

            $controller = new $controllerClass($this->app);

            if (!method_exists($controller, $method)) {
                throw new \RuntimeException("Method '{$method}' not found in controller '{$controllerClass}'.");
            }

            call_user_func_array([$controller, $method], array_merge([$request], $params));
            return;
        }

        throw new \RuntimeException("Invalid route action.");
    }

    /**
     * Send a 404 response.
     */
    private function send404(): void
    {
        http_response_code(404);
        
        $errorView = BASE_PATH . '/resources/views/errors/404.php';
        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo '<h1>404 - Page Not Found</h1>';
        }
        exit;
    }
}
