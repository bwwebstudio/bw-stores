<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Mail Service
 * 
 * Abstraction over PHPMailer for sending emails.
 * Gracefully handles missing SMTP configuration.
 */
class MailService
{
    private array $config;
    private bool $isConfigured;

    public function __construct()
    {
        $this->config = require BASE_PATH . '/config/mail.php';
        $this->isConfigured = !empty($this->config['host']) && !empty($this->config['username']);
    }

    /**
     * Send a verification email.
     */
    public function sendVerificationEmail(array $user): bool
    {
        $verifyUrl = url('verify-email/' . $user['verification_token']);

        $subject = 'Verify Your Email - BW Store';
        $body = $this->renderTemplate('verification', [
            'name'       => e($user['name']),
            'verify_url' => $verifyUrl,
        ]);

        return $this->send($user['email'], $user['name'], $subject, $body);
    }

    /**
     * Send a password reset email.
     */
    public function sendPasswordResetEmail(array $user, string $token): bool
    {
        $resetUrl = url('reset-password/' . $token);

        $subject = 'Reset Your Password - BW Store';
        $body = $this->renderTemplate('password-reset', [
            'name'      => e($user['name']),
            'reset_url' => $resetUrl,
        ]);

        return $this->send($user['email'], $user['name'], $subject, $body);
    }

    /**
     * Send a welcome email after successful registration.
     */
    public function sendWelcomeEmail(array $user): bool
    {
        $dashboardUrl = url('dashboard');

        $subject = 'Welcome to BW Store!';
        $body = $this->renderTemplate('welcome', [
            'name'          => e($user['name']),
            'dashboard_url' => $dashboardUrl,
        ]);

        return $this->send($user['email'], $user['name'], $subject, $body);
    }

    /**
     * Send an email using PHPMailer.
     */
    private function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        if (!$this->isConfigured) {
            app_log("Mail not configured. Would have sent '{$subject}' to {$toEmail}", 'INFO', 'mail.log');
            return false;
        }

        try {
            $mail = new PHPMailer(true);

            // SMTP settings
            $mail->isSMTP();
            $mail->Host       = $this->config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->config['username'];
            $mail->Password   = $this->config['password'];
            $mail->SMTPSecure = $this->config['encryption'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $this->config['port'];

            // Recipients
            $mail->setFrom($this->config['from']['address'], $this->config['from']['name']);
            $mail->addAddress($toEmail, $toName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $htmlBody));

            $mail->send();

            app_log("Email sent: '{$subject}' to {$toEmail}", 'INFO', 'mail.log');
            return true;

        } catch (Exception $e) {
            app_log("Email failed: '{$subject}' to {$toEmail} - " . $e->getMessage(), 'ERROR', 'mail.log');
            return false;
        }
    }

    /**
     * Render an email template with variables.
     */
    private function renderTemplate(string $template, array $data): string
    {
        $brandColor = '#2563EB';
        $appName = config('app.name', 'BW Store');

        // Common email wrapper
        $header = "
        <div style=\"font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff;\">
            <div style=\"background: {$brandColor}; padding: 24px 32px; text-align: center;\">
                <h1 style=\"color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;\">BW Store</h1>
            </div>
            <div style=\"padding: 32px;\">";

        $footer = "
            </div>
            <div style=\"background: #f8fafc; padding: 20px 32px; text-align: center; border-top: 1px solid #e2e8f0;\">
                <p style=\"color: #94a3b8; font-size: 13px; margin: 0;\">© " . date('Y') . " BW Web Studio. All rights reserved.</p>
            </div>
        </div>";

        $content = match ($template) {
            'verification' => "
                <h2 style=\"color: #0f172a; font-size: 20px; margin: 0 0 16px;\">Verify Your Email</h2>
                <p style=\"color: #475569; font-size: 15px; line-height: 1.6;\">Hi {$data['name']},</p>
                <p style=\"color: #475569; font-size: 15px; line-height: 1.6;\">Thank you for signing up for BW Store. Please verify your email address by clicking the button below:</p>
                <div style=\"text-align: center; margin: 28px 0;\">
                    <a href=\"{$data['verify_url']}\" style=\"display: inline-block; background: {$brandColor}; color: #ffffff; text-decoration: none; padding: 12px 32px; border-radius: 8px; font-weight: 600; font-size: 15px;\">Verify Email</a>
                </div>
                <p style=\"color: #94a3b8; font-size: 13px; line-height: 1.6;\">If you didn't create an account, you can ignore this email.</p>",

            'password-reset' => "
                <h2 style=\"color: #0f172a; font-size: 20px; margin: 0 0 16px;\">Reset Your Password</h2>
                <p style=\"color: #475569; font-size: 15px; line-height: 1.6;\">Hi {$data['name']},</p>
                <p style=\"color: #475569; font-size: 15px; line-height: 1.6;\">We received a request to reset your password. Click the button below to set a new password:</p>
                <div style=\"text-align: center; margin: 28px 0;\">
                    <a href=\"{$data['reset_url']}\" style=\"display: inline-block; background: {$brandColor}; color: #ffffff; text-decoration: none; padding: 12px 32px; border-radius: 8px; font-weight: 600; font-size: 15px;\">Reset Password</a>
                </div>
                <p style=\"color: #94a3b8; font-size: 13px; line-height: 1.6;\">This link will expire in 1 hour. If you didn't request this, please ignore this email.</p>",

            'welcome' => "
                <h2 style=\"color: #0f172a; font-size: 20px; margin: 0 0 16px;\">Welcome to BW Store! 🎉</h2>
                <p style=\"color: #475569; font-size: 15px; line-height: 1.6;\">Hi {$data['name']},</p>
                <p style=\"color: #475569; font-size: 15px; line-height: 1.6;\">Your BW Store account is ready. Start building your online store today!</p>
                <div style=\"text-align: center; margin: 28px 0;\">
                    <a href=\"{$data['dashboard_url']}\" style=\"display: inline-block; background: {$brandColor}; color: #ffffff; text-decoration: none; padding: 12px 32px; border-radius: 8px; font-weight: 600; font-size: 15px;\">Go to Dashboard</a>
                </div>",

            default => "<p>Email content not found.</p>",
        };

        return $header . $content . $footer;
    }
}
