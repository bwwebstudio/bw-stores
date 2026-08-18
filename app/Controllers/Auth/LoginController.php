<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Core\Request;
use App\Services\AuthService;

/**
 * Login Controller
 */
class LoginController extends Controller
{
    /**
     * Show the login form.
     * GET /login
     */
    public function showForm(Request $request): void
    {
        // Redirect if already logged in
        if (is_authenticated()) {
            if (is_admin()) {
                $this->redirect(url('admin'));
            } else {
                $this->redirect(url('dashboard'));
            }
            return;
        }

        $this->view('auth.login');
    }

    /**
     * Process login.
     * POST /login
     */
    public function login(Request $request): void
    {
        $email = sanitize_email($request->input('email', ''));
        $password = $request->input('password', '');

        // Basic validation
        $errors = [];
        if (empty($email)) {
            $errors['email'] = 'Email is required.';
        }
        if (empty($password)) {
            $errors['password'] = 'Password is required.';
        }

        if (!empty($errors)) {
            $this->backWithErrors($errors, ['email' => $email]);
            return;
        }

        // Attempt login
        $authService = new AuthService();
        $result = $authService->login($email, $password);

        if (!$result['success']) {
            $this->backWithErrors($result['errors'], ['email' => $email]);
            return;
        }

        // Clear rate limit on successful login
        \App\Middleware\RateLimitMiddleware::clearAttempts($request);

        // Success! Flash message and redirect
        flash('success', 'Welcome back, ' . e($result['user']['name']) . '!');

        // Check for intended URL
        $intendedUrl = session()->get('intended_url');
        session()->remove('intended_url');

        if ($result['user']['role'] === 'admin') {
            $this->redirect(url('admin'));
        } elseif ($intendedUrl) {
            $this->redirect(url(ltrim($intendedUrl, '/')));
        } else {
            $this->redirect(url('dashboard'));
        }
    }
}
