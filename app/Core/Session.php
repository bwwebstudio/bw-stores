<?php

namespace App\Core;

/**
 * Session
 * 
 * Secure session management with HttpOnly, Secure, SameSite cookies.
 * Supports flash messages, session timeout, and ID regeneration.
 */
class Session
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Start the session with secure settings.
     */
    public function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Configure session cookie parameters safely
        $cookieParams = [
            'lifetime' => (int)($this->config['lifetime'] ?? 120) * 60,
            'path'     => $this->config['path'] ?? '/',
            'secure'   => !empty($this->config['secure']) && $this->config['secure'] === true,
            'httponly' => $this->config['httponly'] ?? true,
            'samesite' => $this->config['samesite'] ?? 'Lax',
        ];

        // CRITICAL FIX: Only pass domain if not empty string.
        // An empty string domain in PHP causes browsers on localhost/127.0.0.1 to drop cookies!
        if (!empty($this->config['domain']) && trim($this->config['domain']) !== '') {
            $cookieParams['domain'] = trim($this->config['domain']);
        }

        session_set_cookie_params($cookieParams);

        $sessionName = $this->config['name'] ?? 'bw_store_session';
        session_name($sessionName);

        // Strict mode settings
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');

        if (!headers_sent()) {
            session_start();
        }

        // Ensure CSRF token is initialized on every session
        $this->csrfToken();

        // Check session timeout
        $this->checkTimeout();

        // Process flash messages (mark previous flash for removal)
        $this->ageFlashData();
    }

    /**
     * Get a session value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set a session value.
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Check if a session key exists.
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session value.
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Set flash data (available for the next request only).
     */
    public function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash']['new'][$key] = $value;
    }

    /**
     * Get flash data.
     */
    public function getFlash(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash']['old'][$key] 
            ?? $_SESSION['_flash']['new'][$key] 
            ?? $default;
    }

    /**
     * Check if flash data exists.
     */
    public function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash']['old'][$key]) 
            || isset($_SESSION['_flash']['new'][$key]);
    }

    /**
     * Preserve old form input for redisplay after validation errors.
     */
    public function flashInput(array $input): void
    {
        // Remove sensitive fields before flashing
        unset($input['password'], $input['password_confirmation'], $input['_csrf_token']);
        $this->flash('_old_input', $input);
    }

    /**
     * Get old input value (for form repopulation).
     */
    public function oldInput(string $key, mixed $default = null): mixed
    {
        $old = $this->getFlash('_old_input', []);
        return $old[$key] ?? $default;
    }

    /**
     * Set validation errors for flash display.
     */
    public function flashErrors(array $errors): void
    {
        $this->flash('_errors', $errors);
    }

    /**
     * Get validation errors.
     */
    public function getErrors(): array
    {
        return $this->getFlash('_errors', []);
    }

    /**
     * Check if there are validation errors.
     */
    public function hasErrors(): bool
    {
        $errors = $this->getErrors();
        return !empty($errors);
    }

    /**
     * Regenerate the session ID (call after login).
     */
    public function regenerate(): void
    {
        $oldCsrf = $_SESSION['_csrf_token'] ?? null;

        if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
            session_regenerate_id(true);
        }

        if ($oldCsrf) {
            $_SESSION['_csrf_token'] = $oldCsrf;
        }

        $_SESSION['_last_activity'] = time();
    }

    /**
     * Destroy the session completely.
     */
    public function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies') && !headers_sent()) {
            $params = session_get_cookie_params();
            $cookieDomain = !empty($params['domain']) ? $params['domain'] : '';
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $cookieDomain,
                $params['secure'],
                $params['httponly']
            );
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * Get the CSRF token (generate if not exists).
     */
    public function csrfToken(): string
    {
        if (!isset($_SESSION['_csrf_token']) || empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    /**
     * Verify a CSRF token.
     */
    public function verifyCsrfToken(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $sessionToken = $_SESSION['_csrf_token'] ?? '';
        if (empty($sessionToken)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    /**
     * Age flash data — move 'new' to 'old', clear previous 'old'.
     */
    private function ageFlashData(): void
    {
        // Previous 'new' becomes 'old'
        $_SESSION['_flash']['old'] = $_SESSION['_flash']['new'] ?? [];
        $_SESSION['_flash']['new'] = [];
    }

    /**
     * Check for session timeout.
     */
    private function checkTimeout(): void
    {
        $lifetime = (int)($this->config['lifetime'] ?? 120) * 60; // Convert minutes to seconds

        if (isset($_SESSION['_last_activity'])) {
            if (time() - $_SESSION['_last_activity'] > $lifetime) {
                $this->destroy();
                if (!headers_sent()) {
                    session_start();
                }
            }
        }

        $_SESSION['_last_activity'] = time();
    }
}
