<?php

namespace App\Middleware;

use App\Core\Application;
use App\Core\Request;
use App\Core\Response;

/**
 * CSRF Middleware
 * 
 * Validates CSRF tokens on all state-changing requests (POST, PUT, PATCH, DELETE).
 * Protects against cross-site request forgery attacks.
 */
class CsrfMiddleware
{
    /**
     * Methods that require CSRF verification.
     */
    private const PROTECTED_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Routes exempt from CSRF verification.
     * (e.g., webhook endpoints that verify their own signatures)
     */
    private const EXEMPT_ROUTES = [
        '/webhooks/razorpay',
    ];

    /**
     * Handle the request.
     * Returns false to halt the middleware pipeline.
     */
    public function handle(Request $request, Application $app): bool
    {
        $method = $request->method();

        // Only verify on state-changing methods
        if (!in_array($method, self::PROTECTED_METHODS)) {
            return true;
        }

        // Skip exempt routes
        $uri = $request->uri();
        foreach (self::EXEMPT_ROUTES as $exempt) {
            if (str_starts_with($uri, $exempt)) {
                return true;
            }
        }

        // Get the CSRF token from the request
        $token = $request->input('_csrf_token')
            ?? $request->server('HTTP_X_CSRF_TOKEN', '');

        // Verify the token
        $session = $app->getSession();
        if (empty($token) || !$session->verifyCsrfToken($token)) {
            app_log("CSRF verification failed for {$method} {$uri} from IP {$request->ip()}", 'WARNING', 'security.log');

            if ($request->isAjax() || $request->expectsJson()) {
                Response::json(['error' => 'CSRF token mismatch.'], 419);
                return false;
            }

            // For web forms, ensure a new CSRF token is generated and provide clear recovery
            $session->csrfToken();

            // If coming from login or signup, redirect back with flash message instead of breaking
            $referer = $request->server('HTTP_REFERER', '');
            if (!empty($referer)) {
                $session->flash('error', 'Session refreshed for security. Please submit the form again.');
                Response::redirect($referer);
                return false;
            }

            Response::csrfExpired();
            return false;
        }

        return true;
    }
}
