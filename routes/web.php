<?php

/**
 * Web Routes
 * 
 * All application routes: Public Storefront, Merchant Dashboard, Admin Portal, and Auth.
 */

use App\Middleware\CsrfMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\RateLimitMiddleware;

$router = $app->getRouter();

// ─────────────────────────────────────────────
// Public Marketing Landing Page & System Health
// ─────────────────────────────────────────────
$router->get('/', ['App\Controllers\Auth\HomeController', 'index'], 'home');

$router->get('/health', function() use ($app) {
    $dbStatus = 'disconnected';
    $dbError = null;
    $tables = [];
    $vars = [
        'MYSQLHOST'     => !empty(env('MYSQLHOST')),
        'MYSQLPORT'     => env('MYSQLPORT', 'not set'),
        'MYSQLDATABASE' => env('MYSQLDATABASE', 'not set'),
        'MYSQLUSER'     => !empty(env('MYSQLUSER')),
        'MYSQL_URL'     => !empty(env('MYSQL_URL')),
        'DATABASE_URL'  => !empty(env('DATABASE_URL')),
        'DB_HOST'       => env('DB_HOST', 'not set'),
    ];
    try {
        $db = $app->getDatabase();
        $dbStatus = 'connected';
        $rawTables = $db->fetchAll("SHOW TABLES");
        $tables = array_map(fn($r) => array_values($r)[0], $rawTables);
    } catch (\Throwable $e) {
        $dbError = $e->getMessage();
    }
    json_response([
        'status'       => $dbStatus === 'connected' ? 'ok' : 'error',
        'database'     => $dbStatus,
        'error'        => $dbError,
        'tables_count' => count($tables),
        'tables'       => $tables,
        'env_detected' => $vars,
    ]);
}, 'health');

// ─────────────────────────────────────────────
// Public Merchant Auth Routes
// ─────────────────────────────────────────────
$router->get('/login', ['App\Controllers\Auth\LoginController', 'showForm'], 'login');
$router->get('/signup', ['App\Controllers\Auth\RegisterController', 'showForm'], 'signup');
$router->get('/forgot-password', ['App\Controllers\Auth\ForgotPasswordController', 'showForm'], 'forgot-password');
$router->get('/reset-password/{token}', ['App\Controllers\Auth\ForgotPasswordController', 'showReset'], 'reset-password');
$router->get('/verify-email/{token}', ['App\Controllers\Auth\VerifyEmailController', 'verify'], 'verify-email');

$router->group(['middleware' => [CsrfMiddleware::class, RateLimitMiddleware::class]], function ($router) {
    $router->post('/login', ['App\Controllers\Auth\LoginController', 'login'], 'login.post');
    $router->post('/signup', ['App\Controllers\Auth\RegisterController', 'register'], 'signup.post');
    $router->post('/forgot-password', ['App\Controllers\Auth\ForgotPasswordController', 'sendReset'], 'forgot-password.post');
    $router->post('/reset-password', ['App\Controllers\Auth\ForgotPasswordController', 'resetPassword'], 'reset-password.post');
});

$router->group(['middleware' => [CsrfMiddleware::class]], function ($router) {
    $router->post('/logout', ['App\Controllers\Auth\LogoutController', 'logout'], 'logout');
});

// ─────────────────────────────────────────────
// Merchant Dashboard & Management Routes
// ─────────────────────────────────────────────
$router->group(['prefix' => 'dashboard', 'middleware' => [AuthMiddleware::class]], function ($router) {
    // Overview
    $router->get('/', ['App\Controllers\Merchant\DashboardController', 'index'], 'dashboard');

    // Onboarding
    $router->get('/onboarding', ['App\Controllers\Merchant\OnboardingController', 'show'], 'dashboard.onboarding');
    $router->post('/onboarding', ['App\Controllers\Merchant\OnboardingController', 'save'], 'dashboard.onboarding.save');

    // Products
    $router->get('/products', ['App\Controllers\Merchant\ProductController', 'index'], 'dashboard.products');
    $router->get('/products/create', ['App\Controllers\Merchant\ProductController', 'create'], 'dashboard.products.create');
    $router->post('/products/create', ['App\Controllers\Merchant\ProductController', 'store'], 'dashboard.products.store');
    $router->get('/products/{id}/edit', ['App\Controllers\Merchant\ProductController', 'edit'], 'dashboard.products.edit');
    $router->post('/products/{id}/edit', ['App\Controllers\Merchant\ProductController', 'update'], 'dashboard.products.update');
    $router->post('/products/{id}/delete', ['App\Controllers\Merchant\ProductController', 'delete'], 'dashboard.products.delete');

    // Categories
    $router->get('/categories', ['App\Controllers\Merchant\CategoryController', 'index'], 'dashboard.categories');
    $router->post('/categories/create', ['App\Controllers\Merchant\CategoryController', 'store'], 'dashboard.categories.store');
    $router->post('/categories/{id}/edit', ['App\Controllers\Merchant\CategoryController', 'update'], 'dashboard.categories.update');
    $router->post('/categories/{id}/delete', ['App\Controllers\Merchant\CategoryController', 'delete'], 'dashboard.categories.delete');

    // Inventory
    $router->get('/inventory', ['App\Controllers\Merchant\InventoryController', 'index'], 'dashboard.inventory');
    $router->post('/inventory/adjust', ['App\Controllers\Merchant\InventoryController', 'adjust'], 'dashboard.inventory.adjust');

    // Orders
    $router->get('/orders', ['App\Controllers\Merchant\OrderController', 'index'], 'dashboard.orders');
    $router->get('/orders/{id}', ['App\Controllers\Merchant\OrderController', 'show'], 'dashboard.orders.show');
    $router->post('/orders/{id}/status', ['App\Controllers\Merchant\OrderController', 'updateStatus'], 'dashboard.orders.status');
    $router->get('/orders/{id}/invoice', ['App\Controllers\Merchant\OrderController', 'invoice'], 'dashboard.orders.invoice');

    // Customers
    $router->get('/customers', ['App\Controllers\Merchant\CustomerController', 'index'], 'dashboard.customers');
    $router->get('/customers/{id}', ['App\Controllers\Merchant\CustomerController', 'show'], 'dashboard.customers.show');

    // Coupons
    $router->get('/coupons', ['App\Controllers\Merchant\CouponController', 'index'], 'dashboard.coupons');
    $router->post('/coupons/create', ['App\Controllers\Merchant\CouponController', 'store'], 'dashboard.coupons.store');
    $router->post('/coupons/{id}/edit', ['App\Controllers\Merchant\CouponController', 'update'], 'dashboard.coupons.update');
    $router->post('/coupons/{id}/delete', ['App\Controllers\Merchant\CouponController', 'delete'], 'dashboard.coupons.delete');

    // Store Design & Theme
    $router->get('/store-design', ['App\Controllers\Merchant\StoreDesignController', 'index'], 'dashboard.store_design');
    $router->post('/store-design', ['App\Controllers\Merchant\StoreDesignController', 'update'], 'dashboard.store_design.update');

    // Analytics
    $router->get('/analytics', ['App\Controllers\Merchant\AnalyticsController', 'index'], 'dashboard.analytics');

    // Payments
    $router->get('/payments', ['App\Controllers\Merchant\PaymentController', 'index'], 'dashboard.payments');
    $router->post('/payments', ['App\Controllers\Merchant\PaymentController', 'update'], 'dashboard.payments.update');

    // Subscriptions
    $router->get('/subscription', ['App\Controllers\Merchant\SubscriptionController', 'index'], 'dashboard.subscription');
    $router->get('/subscription/checkout', ['App\Controllers\Merchant\SubscriptionController', 'checkout'], 'dashboard.subscription.checkout');
    $router->post('/subscription/pay', ['App\Controllers\Merchant\SubscriptionController', 'pay'], 'dashboard.subscription.pay');

    // Support
    $router->get('/support', ['App\Controllers\Merchant\SupportController', 'index'], 'dashboard.support');
    $router->post('/support/create', ['App\Controllers\Merchant\SupportController', 'create'], 'dashboard.support.create');
    $router->get('/support/{id}', ['App\Controllers\Merchant\SupportController', 'show'], 'dashboard.support.show');
    $router->post('/support/{id}/reply', ['App\Controllers\Merchant\SupportController', 'reply'], 'dashboard.support.reply');

    // Settings
    $router->get('/settings', ['App\Controllers\Merchant\SettingsController', 'index'], 'dashboard.settings');
    $router->post('/settings/profile', ['App\Controllers\Merchant\SettingsController', 'updateProfile'], 'dashboard.settings.profile');
    $router->post('/settings/password', ['App\Controllers\Merchant\SettingsController', 'updatePassword'], 'dashboard.settings.password');
});

// ─────────────────────────────────────────────
// Admin Portal Routes
// ─────────────────────────────────────────────
$router->get('/admin/login', ['App\Controllers\Admin\AdminAuthController', 'showLogin'], 'admin.login');

$router->group(['middleware' => [CsrfMiddleware::class, RateLimitMiddleware::class]], function ($router) {
    $router->post('/admin/login', ['App\Controllers\Admin\AdminAuthController', 'login'], 'admin.login.post');
});

$router->group(['prefix' => 'admin', 'middleware' => [AdminMiddleware::class]], function ($router) {
    $router->get('/', ['App\Controllers\Admin\AdminDashboardController', 'index'], 'admin.dashboard');
    $router->post('/logout', ['App\Controllers\Admin\AdminAuthController', 'logout'], 'admin.logout');

    // Merchants
    $router->get('/merchants', ['App\Controllers\Admin\AdminMerchantController', 'index'], 'admin.merchants');
    $router->get('/merchants/{id}', ['App\Controllers\Admin\AdminMerchantController', 'show'], 'admin.merchants.show');
    $router->post('/merchants/{id}/suspend', ['App\Controllers\Admin\AdminMerchantController', 'suspend'], 'admin.merchants.suspend');
    $router->post('/merchants/{id}/activate', ['App\Controllers\Admin\AdminMerchantController', 'activate'], 'admin.merchants.activate');
    $router->post('/merchants/{id}/extend', ['App\Controllers\Admin\AdminMerchantController', 'extendSubscription'], 'admin.merchants.extend');
    $router->post('/merchants/{id}/delete', ['App\Controllers\Admin\AdminMerchantController', 'delete'], 'admin.merchants.delete');
    $router->post('/merchants/{id}/reset-password', ['App\Controllers\Admin\AdminMerchantController', 'resetPassword'], 'admin.merchants.reset_password');
    $router->post('/merchants/{id}/toggle-store', ['App\Controllers\Admin\AdminMerchantController', 'toggleStore'], 'admin.merchants.toggle_store');

    // Stores
    $router->get('/stores', ['App\Controllers\Admin\AdminStoreController', 'index'], 'admin.stores');

    // Subscriptions
    $router->get('/subscriptions', ['App\Controllers\Admin\AdminSubscriptionController', 'index'], 'admin.subscriptions');

    // Payments
    $router->get('/payments', ['App\Controllers\Admin\AdminPaymentController', 'index'], 'admin.payments');
    $router->post('/payments/{id}/approve', ['App\Controllers\Admin\AdminPaymentController', 'approve'], 'admin.payments.approve');
    $router->post('/payments/{id}/reject', ['App\Controllers\Admin\AdminPaymentController', 'reject'], 'admin.payments.reject');

    // Orders
    $router->get('/orders', ['App\Controllers\Admin\AdminOrderController', 'index'], 'admin.orders');

    // Support
    $router->get('/support', ['App\Controllers\Admin\AdminSupportController', 'index'], 'admin.support');
    $router->get('/support/{id}', ['App\Controllers\Admin\AdminSupportController', 'show'], 'admin.support.show');
    $router->post('/support/{id}/reply', ['App\Controllers\Admin\AdminSupportController', 'reply'], 'admin.support.reply');

    // Announcements
    $router->get('/announcements', ['App\Controllers\Admin\AdminAnnouncementController', 'index'], 'admin.announcements');
    $router->post('/announcements/create', ['App\Controllers\Admin\AdminAnnouncementController', 'store'], 'admin.announcements.store');
    $router->post('/announcements/{id}/delete', ['App\Controllers\Admin\AdminAnnouncementController', 'delete'], 'admin.announcements.delete');

    // Audit Logs
    $router->get('/audit-logs', ['App\Controllers\Admin\AdminAuditLogController', 'index'], 'admin.audit_logs');

    // Settings
    $router->get('/settings', ['App\Controllers\Admin\AdminSettingController', 'index'], 'admin.settings');
    $router->post('/settings', ['App\Controllers\Admin\AdminSettingController', 'update'], 'admin.settings.update');
});

// ─────────────────────────────────────────────
// Public Storefront Routes (/store/{slug}/...)
// ─────────────────────────────────────────────
$router->get('/store/{slug}', ['App\Controllers\Store\StoreFrontController', 'home'], 'store.home');
$router->get('/store/{slug}/products', ['App\Controllers\Store\StoreFrontController', 'products'], 'store.products');
$router->get('/store/{slug}/product/{product_slug}', ['App\Controllers\Store\StoreFrontController', 'product'], 'store.product');
$router->get('/store/{slug}/category/{category_slug}', ['App\Controllers\Store\StoreFrontController', 'category'], 'store.category');
$router->get('/store/{slug}/cart', ['App\Controllers\Store\StoreFrontController', 'cart'], 'store.cart');
$router->post('/store/{slug}/cart/add', ['App\Controllers\Store\StoreFrontController', 'addToCart'], 'store.cart.add');
$router->post('/store/{slug}/cart/update', ['App\Controllers\Store\StoreFrontController', 'updateCart'], 'store.cart.update');
$router->post('/store/{slug}/cart/coupon', ['App\Controllers\Store\StoreFrontController', 'applyCoupon'], 'store.cart.coupon');
$router->get('/store/{slug}/checkout', ['App\Controllers\Store\StoreFrontController', 'checkout'], 'store.checkout');
$router->post('/store/{slug}/checkout', ['App\Controllers\Store\StoreFrontController', 'processCheckout'], 'store.checkout.process');
$router->get('/store/{slug}/order-success/{order_number}', ['App\Controllers\Store\StoreFrontController', 'orderSuccess'], 'store.order_success');
