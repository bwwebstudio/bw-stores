<?php

namespace App\Middleware;

use App\Core\Application;
use App\Core\Request;
use App\Core\Response;

/**
 * Auth Middleware
 * 
 * Ensures the user is authenticated as a merchant.
 * Redirects to login if not authenticated.
 */
class AuthMiddleware
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
                Response::json(['error' => 'Authentication required.'], 401);
            } else {
                $session->flash('error', 'Please log in to continue.');
                // Store intended URL for redirect after login
                $session->set('intended_url', $request->uri());
                Response::redirect(url('login'));
            }
            return false;
        }

        // Check if user is a merchant
        if ($session->get('user_role') !== 'merchant') {
            Response::forbidden();
            return false;
        }

        // Check if merchant account is active
        $merchantStatus = $session->get('merchant_status', 'pending');
        if ($merchantStatus === 'suspended') {
            if ($request->isAjax() || $request->expectsJson()) {
                Response::json(['error' => 'Your account has been suspended.'], 403);
            } else {
                $session->flash('error', 'Your account has been suspended. Please contact support.');
                Response::redirect(url('login'));
            }
            return false;
        }

        // Get merchant record from database
        $merchantId = $session->get('merchant_id');
        if ($merchantId) {
            $db = $app->getDatabase();
            $merchant = $db->fetchOne("SELECT * FROM merchants WHERE id = ?", [$merchantId]);
            if ($merchant && !$merchant['onboarding_completed']) {
                $uri = $request->uri();
                if ($uri !== '/dashboard/onboarding' && $uri !== '/logout') {
                    $session->flash('info', 'Please complete your store setup and subscription payment to access your dashboard.');
                    Response::redirect(url('dashboard/onboarding'));
                    return false;
                }
            }

            // If onboarding is completed, verify active or trialing subscription
            if ($merchant && $merchant['onboarding_completed']) {
                $sub = $db->fetchOne("SELECT * FROM subscriptions WHERE merchant_id = ? ORDER BY id DESC LIMIT 1", [$merchantId]);
                $isExpired = false;
                if (!$sub || !in_array($sub['status'], ['active', 'trialing'])) {
                    $isExpired = true;
                } elseif (!empty($sub['current_period_end']) && strtotime($sub['current_period_end']) < time()) {
                    $isExpired = true;
                }

                if ($isExpired) {
                    $uri = $request->uri();
                    if ($uri !== '/dashboard/subscription' && $uri !== '/dashboard/subscription/checkout' && $uri !== '/dashboard/subscription/pay' && $uri !== '/logout' && $uri !== '/dashboard/support') {
                        $session->flash('error', '⏳ Your subscription or 7-day free trial has expired. Please choose a plan to reactivate your store.');
                        Response::redirect(url('dashboard/subscription'));
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
