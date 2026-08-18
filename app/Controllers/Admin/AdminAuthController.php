<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Services\AuthService;

/**
 * Admin Auth Controller
 * 
 * Separate login flow for admin panel.
 */
class AdminAuthController extends Controller
{
    /**
     * Show admin login form.
     * GET /admin/login
     */
    public function showLogin(Request $request): void
    {
        // Redirect if already logged in as admin
        if (is_authenticated() && is_admin()) {
            $this->redirect(url('admin'));
            return;
        }

        $this->view('admin.login');
    }

    /**
     * Process admin login.
     * POST /admin/login
     */
    public function login(Request $request): void
    {
        $email = sanitize_email($request->input('email', ''));
        $password = $request->input('password', '');

        $errors = [];
        if (empty($email)) $errors['email'] = 'Email is required.';
        if (empty($password)) $errors['password'] = 'Password is required.';

        if (!empty($errors)) {
            $this->backWithErrors($errors, ['email' => $email]);
            return;
        }

        $authService = new AuthService();
        $result = $authService->login($email, $password);

        if (!$result['success']) {
            $this->backWithErrors($result['errors'], ['email' => $email]);
            return;
        }

        // Verify admin role
        if ($result['user']['role'] !== 'admin') {
            $authService->logout();
            $this->backWithErrors(['general' => 'Admin access required. This account does not have admin privileges.'], ['email' => $email]);
            return;
        }

        \App\Middleware\RateLimitMiddleware::clearAttempts($request);

        flash('success', 'Welcome back, Admin!');
        $this->redirect(url('admin'));
    }

    /**
     * Process admin logout.
     * POST /admin/logout
     */
    public function logout(Request $request): void
    {
        $authService = new AuthService();
        $authService->logout();

        flash('success', 'Logged out successfully.');
        $this->redirect(url('admin/login'));
    }
}
