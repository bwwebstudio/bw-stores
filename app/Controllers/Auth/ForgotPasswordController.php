<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Core\Request;
use App\Services\AuthService;

/**
 * Forgot Password Controller
 */
class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form.
     * GET /forgot-password
     */
    public function showForm(Request $request): void
    {
        $this->view('auth.forgot-password');
    }

    /**
     * Send the password reset link.
     * POST /forgot-password
     */
    public function sendReset(Request $request): void
    {
        $email = sanitize_email($request->input('email', ''));

        if (empty($email) || !is_valid_email($email)) {
            $this->backWithErrors(['email' => 'Please enter a valid registered email address.'], ['email' => $email]);
            return;
        }

        $db = $this->app->getDatabase();
        $user = $db->fetchOne("SELECT u.id as user_id, u.name, u.email, m.id as merchant_id, m.business_name FROM users u LEFT JOIN merchants m ON m.user_id = u.id WHERE u.email = ?", [$email]);

        if ($user && !empty($user['merchant_id'])) {
            $merchantId = (int)$user['merchant_id'];
            $storeName = $user['business_name'] ?: $user['name'];

            // Log support ticket for admin
            $ticketNumber = 'TK-' . strtoupper(bin2hex(random_bytes(3)));
            $ticketId = $db->insert('support_tickets', [
                'merchant_id'   => $merchantId,
                'ticket_number' => $ticketNumber,
                'subject'       => "🔐 URGENT: Password Reset Assistance Requested ({$email})",
                'category'      => 'account',
                'priority'      => 'urgent',
                'status'        => 'open',
            ]);

            $db->insert('support_messages', [
                'ticket_id' => $ticketId,
                'user_id'   => $user['user_id'],
                'sender'    => 'merchant',
                'message'   => "Merchant {$user['name']} ({$email}) for store '{$storeName}' requested password reset assistance. Admin can set new password directly in Admin Portal -> Merchants.",
            ]);

            // Notification
            $db->insert('notifications', [
                'merchant_id' => $merchantId,
                'title'       => '🔐 Password Reset Request Logged',
                'message'     => "Your password reset request has been received by Admin Support (Ticket #{$ticketNumber}). Contact Admin to receive your updated credentials.",
                'type'        => 'warning',
            ]);

            // Audit log
            $db->insert('audit_logs', [
                'user_id'    => $user['user_id'],
                'action'     => 'merchant_forgot_password_requested',
                'details'    => "Password reset request submitted for {$email}",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Web',
            ]);
        }

        flash('success', '🔐 Password reset support request submitted! Admin will verify your merchant account and assist you. You can also contact Admin directly on WhatsApp for instant assistance.');
        $this->redirect(url('forgot-password'));
    }

    /**
     * Show the reset password form.
     * GET /reset-password/{token}
     */
    public function showReset(Request $request, string $token): void
    {
        $this->view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Process the password reset.
     * POST /reset-password
     */
    public function resetPassword(Request $request): void
    {
        $token = sanitize_input($request->input('token', ''));
        $password = $request->input('password', '');
        $passwordConfirmation = $request->input('password_confirmation', '');

        $authService = new AuthService();
        $result = $authService->resetPassword($token, $password, $passwordConfirmation);

        if (!$result['success']) {
            $this->backWithErrors($result['errors']);
            return;
        }

        flash('success', 'Password reset successfully! You can now log in with your new password.');
        $this->redirect(url('login'));
    }
}
