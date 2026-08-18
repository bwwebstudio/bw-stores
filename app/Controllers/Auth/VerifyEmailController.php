<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Core\Request;
use App\Services\AuthService;

/**
 * Verify Email Controller
 */
class VerifyEmailController extends Controller
{
    /**
     * Verify email address.
     * GET /verify-email/{token}
     */
    public function verify(Request $request, string $token): void
    {
        $authService = new AuthService();
        $result = $authService->verifyEmail($token);

        if ($result['success']) {
            flash('success', $result['message']);
        } else {
            flash('error', $result['message']);
        }

        $this->redirect(url('login'));
    }
}
