<?php

/**
 * Authentication Helper Functions
 * 
 * Convenient functions for checking auth state and roles.
 * Auto-loaded via Composer.
 */

use App\Core\Response;

/**
 * Check if a user is authenticated.
 */
function is_authenticated(): bool
{
    return session()->has('user_id');
}

/**
 * Get the current authenticated user's ID.
 */
function current_user_id(): ?int
{
    return session()->get('user_id');
}

/**
 * Get the current authenticated user's data from session.
 */
function current_user(): ?array
{
    if (!is_authenticated()) {
        return null;
    }

    return [
        'id'    => session()->get('user_id'),
        'name'  => session()->get('user_name'),
        'email' => session()->get('user_email'),
        'role'  => session()->get('user_role'),
    ];
}

/**
 * Get the current merchant's ID from session.
 */
function current_merchant_id(): ?int
{
    return session()->get('merchant_id');
}

/**
 * Get the current merchant data from session.
 */
function current_merchant(): ?array
{
    if (!current_merchant_id()) {
        return null;
    }

    return [
        'id'            => session()->get('merchant_id'),
        'business_name' => session()->get('merchant_business_name'),
        'status'        => session()->get('merchant_status'),
    ];
}

/**
 * Get the current store data from session.
 */
function current_store(): ?array
{
    if (session()->has('store_id') && session()->has('store_slug')) {
        return [
            'id'   => session()->get('store_id'),
            'name' => session()->get('store_name'),
            'slug' => session()->get('store_slug'),
        ];
    }

    $merchantId = current_merchant_id();
    if ($merchantId) {
        $storeModel = new \App\Models\Store();
        $store = $storeModel->findByMerchantId($merchantId);
        if ($store) {
            session()->set('store_id', (int)$store['id']);
            session()->set('store_name', $store['name']);
            session()->set('store_slug', $store['slug']);
            return [
                'id'   => (int)$store['id'],
                'name' => $store['name'],
                'slug' => $store['slug'],
            ];
        }
    }

    return null;
}

/**
 * Check if current user is a merchant.
 */
function is_merchant(): bool
{
    return session()->get('user_role') === 'merchant';
}

/**
 * Check if current user is an admin.
 */
function is_admin(): bool
{
    return session()->get('user_role') === 'admin';
}

/**
 * Require the user to be authenticated.
 * Redirects to login if not.
 */
function require_auth(): void
{
    if (!is_authenticated()) {
        flash('error', 'Please log in to continue.');
        redirect(url('login'));
    }
}

/**
 * Require the user to be a merchant.
 * Redirects to login if not authenticated, 403 if not merchant.
 */
function require_merchant(): void
{
    require_auth();

    if (!is_merchant()) {
        Response::forbidden();
    }
}

/**
 * Require the user to be an admin.
 * Redirects to admin login if not authenticated or not admin.
 */
function require_admin(): void
{
    if (!is_authenticated() || !is_admin()) {
        flash('error', 'Admin access required.');
        redirect(url('admin/login'));
    }
}

/**
 * Require the merchant to be the owner of a given store.
 * This is critical for multi-tenant security.
 */
function require_store_owner(int $storeId): void
{
    require_merchant();
    
    $currentStoreId = session()->get('store_id');
    if ($currentStoreId !== $storeId) {
        Response::forbidden();
    }
}

/**
 * Require an active subscription.
 * For Phase 6 — currently a placeholder that returns true.
 */
function require_subscription_active(): void
{
    require_merchant();
    
    // Phase 6 will implement actual subscription checking.
    // For now, this passes through to avoid blocking development.
    // TODO: Check subscription status when subscription system is built.
}

/**
 * Set auth session data after successful login.
 */
function set_auth_session(array $user, ?array $merchant = null, ?array $store = null): void
{
    $session = session();
    
    // Regenerate session ID to prevent fixation
    $session->regenerate();

    // Set user data
    $session->set('user_id', (int) $user['id']);
    $session->set('user_name', $user['name']);
    $session->set('user_email', $user['email']);
    $session->set('user_role', $user['role']);

    // Set merchant data if available
    if ($merchant) {
        $session->set('merchant_id', (int) $merchant['id']);
        $session->set('merchant_business_name', $merchant['business_name'] ?? '');
        $session->set('merchant_status', $merchant['status'] ?? 'pending');
    }

    // Set store data if available
    if ($store) {
        $session->set('store_id', (int) $store['id']);
        $session->set('store_name', $store['name'] ?? '');
        $session->set('store_slug', $store['slug'] ?? '');
    }
}

/**
 * Clear auth session data (logout).
 */
function clear_auth_session(): void
{
    session()->destroy();
}
