<?php

namespace App\Middleware;

use App\Core\Application;
use App\Core\Request;
use App\Core\Response;

/**
 * Admin Middleware
 * 
 * Ensures the user is authenticated as an admin.
 * Redirects to admin login if not.
 */
class AdminMiddleware
{
    /**
     * Handle the request.
     */
    public function handle(Request $request, Application $app): bool
    {
        $session = $app->getSession();

        // Check if user is logged in
        if (!$session->has('user_id')) {
            if ($request->isAjax() || $request->expectsJson()) {
                Response::json(['error' => 'Admin authentication required.'], 401);
            } else {
                $session->flash('error', 'Please log in to access the admin panel.');
                Response::redirect(url('admin/login'));
            }
            return false;
        }

        // Check if user has admin role
        if ($session->get('user_role') !== 'admin') {
            if ($request->isAjax() || $request->expectsJson()) {
                Response::json(['error' => 'Admin access required.'], 403);
            } else {
                Response::forbidden();
            }
            return false;
        }

        return true;
    }
}
