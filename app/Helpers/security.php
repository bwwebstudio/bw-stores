<?php

/**
 * Security Helper Functions
 * 
 * CSRF, XSS prevention, input sanitization, and secure token generation.
 * Auto-loaded via Composer.
 */

/**
 * HTML-escape a string to prevent XSS.
 * Use this for ALL user-generated content displayed in HTML.
 */
function e(?string $value): string
{
    if ($value === null) {
        return '';
    }
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Get the current CSRF token.
 */
function csrf_token(): string
{
    return session()->csrfToken();
}

/**
 * Generate a hidden CSRF input field for forms.
 */
function csrf_field(): string
{
    $token = csrf_token();
    return '<input type="hidden" name="_csrf_token" value="' . e($token) . '">';
}

/**
 * Generate a hidden method spoofing field.
 */
function method_field(string $method): string
{
    return '<input type="hidden" name="_method" value="' . e(strtoupper($method)) . '">';
}

/**
 * Verify a CSRF token.
 */
function verify_csrf(string $token): bool
{
    return session()->verifyCsrfToken($token);
}

/**
 * Sanitize a string input.
 * Trims whitespace and strips null bytes.
 */
function sanitize_input(?string $value): string
{
    if ($value === null) {
        return '';
    }
    $value = str_replace("\0", '', $value); // Strip null bytes
    return trim($value);
}

/**
 * Sanitize an email address.
 */
function sanitize_email(?string $email): string
{
    if ($email === null) {
        return '';
    }
    return strtolower(trim(filter_var($email, FILTER_SANITIZE_EMAIL)));
}

/**
 * Validate an email address.
 */
function is_valid_email(?string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Sanitize an integer input.
 */
function sanitize_int(mixed $value): int
{
    return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
}

/**
 * Generate a cryptographically secure random token.
 */
function generate_token(int $length = 32): string
{
    return bin2hex(random_bytes($length));
}

/**
 * Generate a secure random string (URL-safe).
 */
function generate_random_string(int $length = 16): string
{
    $bytes = random_bytes(ceil($length / 2));
    return substr(bin2hex($bytes), 0, $length);
}

/**
 * Hash a password securely.
 */
function hash_password(string $password): string
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify a password against a hash.
 */
function verify_password(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

/**
 * Validate password strength.
 * Returns array of error messages (empty if valid).
 */
function validate_password(string $password): array
{
    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number.';
    }

    return $errors;
}

/**
 * Validate a mobile/phone number (basic validation).
 */
function is_valid_mobile(?string $mobile): bool
{
    if ($mobile === null) {
        return false;
    }
    // Allow +, digits, spaces, hyphens. Min 10 digits.
    $digits = preg_replace('/[^0-9]/', '', $mobile);
    return strlen($digits) >= 10 && strlen($digits) <= 15;
}

/**
 * Strip HTML tags but preserve safe formatting.
 */
function clean_html(?string $html): string
{
    if ($html === null) {
        return '';
    }
    return strip_tags($html, '<p><br><strong><em><ul><ol><li><h2><h3><h4><h5><h6>');
}

/**
 * Validate a file upload.
 * Returns array of error messages (empty if valid).
 */
function validate_upload(array $file, array $allowedTypes = [], int $maxSize = 5242880): array
{
    $errors = [];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload failed.';
        return $errors;
    }

    // Check file size
    if ($file['size'] > $maxSize) {
        $errors[] = 'File size exceeds the maximum allowed size of ' . round($maxSize / 1048576, 1) . ' MB.';
    }

    // Check file extension
    if (!empty($allowedTypes)) {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes)) {
            $errors[] = 'File type "' . $extension . '" is not allowed. Allowed: ' . implode(', ', $allowedTypes);
        }
    }

    // Verify MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    $allowedMimes = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'svg'  => 'image/svg+xml',
    ];

    if (!empty($allowedTypes)) {
        $validMimes = array_intersect_key($allowedMimes, array_flip($allowedTypes));
        if (!in_array($mimeType, $validMimes)) {
            $errors[] = 'File MIME type is not allowed.';
        }
    }

    return $errors;
}

/**
 * Generate a safe filename for uploads.
 */
function safe_filename(string $originalName): string
{
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    return generate_random_string(24) . '.' . $extension;
}
