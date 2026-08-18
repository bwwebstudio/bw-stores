<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Core\Request;
use App\Services\AuthService;

/**
 * Logout Controller
 */
class LogoutController extends Controller
{
    /**
     * Process logout.
     * POST /logout
     */
    public function logout(Request $request): void
    {
        $authService = new AuthService();
        $authService->logout();

        // Start a new session for flash message
        session_start();
        flash('success', 'You have been logged out successfully.');

        $this->redirect(url('login'));
    }
}
