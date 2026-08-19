<?php

namespace App\Services;

use App\Core\Application;
use App\Core\Database;
use App\Models\User;
use App\Models\Merchant;

/**
 * Auth Service
 * 
 * Handles all authentication business logic:
 * registration, login, logout, password reset, email verification.
 */
class AuthService
{
    private User $userModel;
    private Merchant $merchantModel;
    private Database $db;

    public function __construct()
    {
        $app = Application::getInstance();
        $this->db = $app->getDatabase();
        $this->userModel = new User($this->db);
        $this->merchantModel = new Merchant($this->db);
    }

    /**
     * Register a new merchant.
     * Creates user + merchant records in a transaction.
     * 
     * @return array{success: bool, errors?: array, user_id?: int, merchant_id?: int}
     */
    public function register(array $data): array
    {
        try {
            // Validate input inside try-catch to catch database errors during validation
            $errors = $this->validateRegistration($data);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            return $this->db->transaction(function (Database $db) use ($data) {
                // Create user
                $userId = $this->userModel->create([
                    'email'    => $data['email'],
                    'password' => $data['password'],
                    'name'     => $data['name'],
                    'mobile'   => $data['mobile'] ?? null,
                    'role'     => 'merchant',
                ]);

                // Create merchant
                $merchantId = $this->merchantModel->create([
                    'user_id' => $userId,
                    'status'  => 'active', // Active under 7-day trial
                ]);

                // Create 7-Day Free Trial on Growth Plan (id: 2)
                $plan = $db->fetchOne("SELECT id FROM plans WHERE slug = 'growth'") ?: $db->fetchOne("SELECT id FROM plans ORDER BY id ASC LIMIT 1");
                if (!$plan) {
                    $planId = $db->insert('plans', [
                        'id'               => 2,
                        'name'             => 'BW Store Growth',
                        'slug'             => 'growth',
                        'price'            => 999.00,
                        'yearly_price'     => 11788.00,
                        'yearly_discount'  => 200.00,
                        'currency'         => 'INR',
                        'billing_interval' => 'monthly',
                        'badge'            => 'Recommended',
                        'description'      => 'The complete powerhouse solution for ambitious brands scaling their revenue rapidly.',
                        'features'         => json_encode(["Unlimited Products & Categories", "All 3 Premium Storefront Themes", "Advanced Real-Time Sales Analytics", "Inventory Tracking & Low Stock Alerts", "Coupons & Dynamic Discount Engine", "Direct Razorpay Connect + UPI + COD", "Priority Email & Ticket Support", "0% Platform Sales Commission"]),
                        'max_products'     => 0,
                        'max_themes'       => 3,
                        'priority_support' => 1,
                        'trial_days'       => 7,
                        'is_active'        => 1,
                    ]);
                } else {
                    $planId = (int)$plan['id'];
                }

                $now = date('Y-m-d H:i:s');
                $trialEnd = date('Y-m-d H:i:s', strtotime('+7 days'));

                $db->insert('subscriptions', [
                    'merchant_id'          => $merchantId,
                    'plan_id'              => $planId,
                    'status'               => 'trialing',
                    'current_period_start' => $now,
                    'current_period_end'   => $trialEnd,
                    'expires_at'           => $trialEnd,
                ]);

                // Welcome Notification
                try {
                    $db->insert('notifications', [
                        'merchant_id' => $merchantId,
                        'title'       => '🎉 Welcome to Your 7-Day Free Trial!',
                        'message'     => 'Your 7-Day Free Trial on BW Store Growth is now active. Set up your store, add products, and start selling with 0% commission!',
                        'type'        => 'success',
                        'link'        => url('dashboard/onboarding'),
                    ]);
                } catch (\Throwable $ne) {
                    app_log("Failed to insert welcome notification: " . $ne->getMessage(), 'WARNING');
                }

                // Get the created user for the verification token
                $user = $this->userModel->findById($userId);

                // Send verification email (non-blocking)
                try {
                    $mailService = new MailService();
                    $mailService->sendVerificationEmail($user);
                } catch (\Throwable $e) {
                    app_log("Failed to send verification email to {$data['email']}: " . $e->getMessage(), 'WARNING');
                }

                // Log the registration
                try {
                    $auditService = new AuditService();
                    $auditService->log('user_registered', 'user', $userId, "New merchant registered with 7-day trial: {$data['email']}");
                } catch (\Throwable $ae) {
                    app_log("Failed to log audit event: " . $ae->getMessage(), 'WARNING');
                }

                return [
                    'success'     => true,
                    'user_id'     => $userId,
                    'merchant_id' => $merchantId,
                ];
            });
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            app_log("Registration failed for " . ($data['email'] ?? 'unknown') . ": {$errorMessage}\n" . $e->getTraceAsString(), 'ERROR');

            return [
                'success' => false,
                'errors'  => ['general' => 'Registration error: ' . $errorMessage],
            ];
        }
    }

    /**
     * Attempt to log in a user.
     * 
     * @return array{success: bool, errors?: array, user?: array}
     */
    public function login(string $email, string $password): array
    {
        $user = $this->userModel->findByEmail($email);

        // User not found
        if (!$user) {
            return [
                'success' => false,
                'errors'  => ['email' => 'No account found with this email address.'],
            ];
        }

        // Check if account is locked (brute force protection)
        if ($this->userModel->isLocked($user)) {
            $lockedUntil = date('h:i A', strtotime($user['locked_until']));
            return [
                'success' => false,
                'errors'  => ['general' => "Account is temporarily locked due to too many failed attempts. Try again after {$lockedUntil}."],
            ];
        }

        // Check if account is active
        if (!$user['is_active']) {
            return [
                'success' => false,
                'errors'  => ['general' => 'Your account has been deactivated. Please contact support.'],
            ];
        }

        // Verify password
        if (!$this->userModel->verifyPassword($user, $password)) {
            $this->userModel->incrementLoginAttempts($user['id']);

            app_log("Failed login attempt for {$email}", 'WARNING', 'security.log');

            return [
                'success' => false,
                'errors'  => ['password' => 'Invalid password. Please try again.'],
            ];
        }

        // Success! Reset login attempts
        $this->userModel->resetLoginAttempts($user['id']);

        // Get merchant data if merchant role
        $merchant = null;
        $store = null;

        if ($user['role'] === 'merchant') {
            $merchant = $this->merchantModel->findByUserId($user['id']);

            // Get store data if exists
            $store = $this->db->fetchOne(
                "SELECT * FROM stores WHERE merchant_id = ?",
                [$merchant['id'] ?? 0]
            );
        }

        // Set session data
        set_auth_session($user, $merchant, $store);

        // Audit log
        $auditService = new AuditService();
        $auditService->log('user_login', 'user', $user['id'], "User logged in: {$email}");

        return [
            'success'  => true,
            'user'     => $user,
            'merchant' => $merchant,
            'store'    => $store,
        ];
    }

    /**
     * Log out the current user.
     */
    public function logout(): void
    {
        $userId = current_user_id();
        $email = session()->get('user_email', 'unknown');

        // Audit log before destroying session
        if ($userId) {
            try {
                $auditService = new AuditService();
                $auditService->log('user_logout', 'user', $userId, "User logged out: {$email}");
            } catch (\Throwable $e) {
                // Don't block logout on audit failure
            }
        }

        clear_auth_session();
    }

    /**
     * Send a password reset link.
     */
    public function forgotPassword(string $email): array
    {
        $user = $this->userModel->findByEmail($email);

        // Always return success to prevent email enumeration
        if (!$user) {
            return ['success' => true];
        }

        $token = generate_token();
        $this->userModel->setResetToken($user['id'], $token);

        try {
            $mailService = new MailService();
            $mailService->sendPasswordResetEmail($user, $token);
        } catch (\Throwable $e) {
            app_log("Failed to send password reset email to {$email}: " . $e->getMessage(), 'WARNING');
        }

        $auditService = new AuditService();
        $auditService->log('password_reset_requested', 'user', $user['id'], "Password reset requested for: {$email}");

        return ['success' => true];
    }

    /**
     * Reset password using a token.
     */
    public function resetPassword(string $token, string $password, string $passwordConfirmation): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }

        if ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $user = $this->userModel->findByResetToken($token);

        if (!$user) {
            return [
                'success' => false,
                'errors'  => ['general' => 'Invalid or expired reset link. Please request a new one.'],
            ];
        }

        $this->userModel->updatePassword($user['id'], $password);

        $auditService = new AuditService();
        $auditService->log('password_reset', 'user', $user['id'], "Password reset for: {$user['email']}");

        return ['success' => true];
    }

    /**
     * Verify an email address.
     */
    public function verifyEmail(string $token): array
    {
        $user = $this->userModel->findByVerificationToken($token);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid or already-used verification link.',
            ];
        }

        $this->userModel->verifyEmail($user['id']);

        $auditService = new AuditService();
        $auditService->log('email_verified', 'user', $user['id'], "Email verified: {$user['email']}");

        return [
            'success' => true,
            'message' => 'Email verified successfully! You can now log in.',
        ];
    }

    /**
     * Validate registration data.
     */
    private function validateRegistration(array $data): array
    {
        $errors = [];

        // Name
        $name = sanitize_input($data['name'] ?? '');
        if (empty($name)) {
            $errors['name'] = 'Name is required.';
        } elseif (strlen($name) > 100) {
            $errors['name'] = 'Name must be 100 characters or less.';
        }

        // Email
        $email = sanitize_email($data['email'] ?? '');
        if (empty($email)) {
            $errors['email'] = 'Email is required.';
        } elseif (!is_valid_email($email)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif ($this->userModel->emailExists($email)) {
            $errors['email'] = 'An account with this email already exists.';
        }

        // Mobile
        $mobile = sanitize_input($data['mobile'] ?? '');
        if (!empty($mobile) && !is_valid_mobile($mobile)) {
            $errors['mobile'] = 'Please enter a valid mobile number.';
        }

        // Password
        $password = $data['password'] ?? '';
        if (empty($password)) {
            $errors['password'] = 'Password is required.';
        } else {
            $passwordErrors = validate_password($password);
            if (!empty($passwordErrors)) {
                $errors['password'] = $passwordErrors[0];
            }
        }

        // Confirm password
        $confirmPassword = $data['password_confirmation'] ?? '';
        if ($password !== $confirmPassword) {
            $errors['password_confirmation'] = 'Passwords do not match.';
        }

        return $errors;
    }
}
