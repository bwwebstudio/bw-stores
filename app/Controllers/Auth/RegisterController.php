<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Core\Request;
use App\Services\AuthService;

/**
 * Register Controller
 */
class RegisterController extends Controller
{
    /**
     * Show the registration form.
     * GET /signup
     */
    public function showForm(Request $request): void
    {
        // Redirect if already logged in
        if (is_authenticated()) {
            $this->redirect(url('dashboard'));
            return;
        }

        $this->view('auth.register');
    }

    /**
     * Process registration.
     * POST /signup
     */
    public function register(Request $request): void
    {
        $data = [
            'name'                  => sanitize_input($request->input('name', '')),
            'email'                 => sanitize_email($request->input('email', '')),
            'mobile'                => sanitize_input($request->input('mobile', '')),
            'password'              => $request->input('password', ''),
            'password_confirmation' => $request->input('password_confirmation', ''),
        ];

        $authService = new AuthService();
        $result = $authService->register($data);

        if (!$result['success']) {
            $this->backWithErrors($result['errors'], $data);
            return;
        }

        // Auto-login after registration
        $loginResult = $authService->login($data['email'], $data['password']);

        if ($loginResult['success']) {
            flash('success', 'Welcome to BW Store! Let\'s set up your store.');
            // Redirect to onboarding (Phase 2, for now go to dashboard)
            $this->redirect(url('dashboard'));
        } else {
            flash('success', 'Account created successfully! Please log in.');
            $this->redirect(url('login'));
        }
    }
}
