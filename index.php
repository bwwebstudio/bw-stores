<?php

/**
 * BW Store - Front Controller
 * 
 * All HTTP requests are routed through this file.
 * This bootstraps the application, loads configuration,
 * and dispatches the request to the router.
 */

// If running with PHP built-in web server, serve existing static files directly
if (php_sapi_name() === 'cli-server') {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $file = __DIR__ . $uri;
    if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
        return false;
    }
}

// Define base path
define('BASE_PATH', __DIR__);

// Autoload dependencies
require BASE_PATH . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

// Load configuration
$config = [
    'app'      => require BASE_PATH . '/config/app.php',
    'database' => require BASE_PATH . '/config/database.php',
    'session'  => require BASE_PATH . '/config/session.php',
    'mail'     => require BASE_PATH . '/config/mail.php',
];

// Set error reporting based on environment
if ($config['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Set timezone
date_default_timezone_set($config['app']['timezone']);

// Set up error and exception handlers
set_exception_handler(function (Throwable $e) use ($config) {
    // Log the error
    $logDir = BASE_PATH . '/storage/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logMessage = sprintf(
        "[%s] %s: %s in %s:%d\nStack trace:\n%s\n\n",
        date('Y-m-d H:i:s'),
        get_class($e),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );
    
    error_log($logMessage, 3, $logDir . '/error.log');
    
    // Show appropriate error page
    http_response_code(500);
    if ($config['app']['debug']) {
        echo '<h1>Error</h1>';
        echo '<p><strong>' . htmlspecialchars(get_class($e)) . ':</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        include BASE_PATH . '/resources/views/errors/500.php';
    }
    exit(1);
});

// Boot the application
$app = new App\Core\Application($config);
$app->boot();

// Load routes
require BASE_PATH . '/routes/web.php';

// Dispatch the request
$app->dispatch();
