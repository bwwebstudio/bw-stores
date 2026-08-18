<?php use App\Core\View; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?php View::yield('title', 'Dashboard'); ?> — BW Store</title>
    
    <!-- Google Fonts: Plus Jakarta Sans, Inter, JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('public/assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= url('public/assets/css/dashboard.css') ?>">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar Overlay (mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <aside class="dashboard-sidebar" id="dashboardSidebar">
            <div class="sidebar-header">
                <a href="<?= url('dashboard') ?>" class="bw-logo">
                    <div class="logo-mark shadow-sm">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 7H20L18.5 21H5.5L4 7Z" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8.5 7V4.5C8.5 3.11929 9.61929 2 11 2H13C14.3807 2 15.5 3.11929 15.5 4.5V7" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                            <path d="M9 13L11.5 15.5L15.5 10.5" stroke="#60A5FA" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="logo-text">BW <span>Store</span> <span class="logo-tag-saas ms-1">MERCHANT</span></div>
                </a>
            </div>

            <?php $store = current_store(); ?>
            <?php if ($store): ?>
            <div style="padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--color-gray-200); background: #f8fafc;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-xs text-muted fw-700" style="letter-spacing:0.05em; text-transform:uppercase;">STOREFRONT</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.65rem; padding: 0.2rem 0.45rem;">LIVE</span>
                </div>
                <div class="fw-700 text-dark text-truncate mb-2" style="font-size: 0.85rem;" title="<?= e($store['name']) ?>"><?= e($store['name']) ?></div>
                <a href="<?= url('store/' . $store['slug']) ?>" target="_blank" class="btn btn-sm btn-primary w-100 fw-700 d-flex align-items-center justify-content-center gap-2 shadow-sm" style="font-size: 0.8rem; padding: 0.45rem 0.75rem; border-radius: 8px;">
                    <i class="bi bi-box-arrow-up-right"></i> View Live Site
                </a>
            </div>
            <?php else: ?>
            <div style="padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--color-gray-200); background: #f8fafc;">
                <a href="<?= url('dashboard/onboarding') ?>" class="btn btn-sm btn-outline-primary w-100 fw-700 d-flex align-items-center justify-content-center gap-2" style="font-size: 0.8rem; padding: 0.45rem 0.75rem; border-radius: 8px;">
                    <i class="bi bi-magic"></i> Setup Your Store Site
                </a>
            </div>
            <?php endif; ?>

            <nav class="sidebar-nav">
                <div class="nav-section">Main</div>
                <a href="<?= url('dashboard') ?>" class="nav-item <?= active_class('/dashboard') ?>">
                    <i class="bi bi-grid-1x2-fill nav-icon"></i> Dashboard
                </a>

                <div class="nav-section">Store</div>
                <a href="<?= url('dashboard/products') ?>" class="nav-item <?= active_class('/dashboard/products') ?>">
                    <i class="bi bi-box-seam nav-icon"></i> Products
                </a>
                <a href="<?= url('dashboard/categories') ?>" class="nav-item <?= active_class('/dashboard/categories') ?>">
                    <i class="bi bi-tags nav-icon"></i> Categories
                </a>
                <a href="<?= url('dashboard/orders') ?>" class="nav-item <?= active_class('/dashboard/orders') ?>">
                    <i class="bi bi-receipt nav-icon"></i> Orders
                </a>
                <a href="<?= url('dashboard/customers') ?>" class="nav-item <?= active_class('/dashboard/customers') ?>">
                    <i class="bi bi-people nav-icon"></i> Customers
                </a>
                <a href="<?= url('dashboard/inventory') ?>" class="nav-item <?= active_class('/dashboard/inventory') ?>">
                    <i class="bi bi-archive nav-icon"></i> Inventory
                </a>
                <a href="<?= url('dashboard/coupons') ?>" class="nav-item <?= active_class('/dashboard/coupons') ?>">
                    <i class="bi bi-ticket-perforated nav-icon"></i> Coupons
                </a>

                <div class="nav-section">Appearance</div>
                <a href="<?= url('dashboard/store-design') ?>" class="nav-item <?= active_class('/dashboard/store-design') ?>">
                    <i class="bi bi-palette nav-icon"></i> Store Design
                </a>

                <div class="nav-section">Reports</div>
                <a href="<?= url('dashboard/analytics') ?>" class="nav-item <?= active_class('/dashboard/analytics') ?>">
                    <i class="bi bi-bar-chart-line nav-icon"></i> Analytics
                </a>

                <div class="nav-section">Account</div>
                <a href="<?= url('dashboard/payments') ?>" class="nav-item <?= active_class('/dashboard/payments') ?>">
                    <i class="bi bi-credit-card nav-icon"></i> Payments
                </a>
                <a href="<?= url('dashboard/subscription') ?>" class="nav-item <?= active_class('/dashboard/subscription') ?>">
                    <i class="bi bi-star nav-icon"></i> Subscription
                </a>
                <a href="<?= url('dashboard/support') ?>" class="nav-item <?= active_class('/dashboard/support') ?>">
                    <i class="bi bi-headset nav-icon"></i> Support
                </a>
                <a href="<?= url('dashboard/settings') ?>" class="nav-item <?= active_class('/dashboard/settings') ?>">
                    <i class="bi bi-gear nav-icon"></i> Settings
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="user-avatar">
                        <?= strtoupper(substr(session()->get('user_name', 'U'), 0, 1)) ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?= e(session()->get('user_name', 'User')) ?></div>
                        <div class="user-email"><?= e(session()->get('user_email', '')) ?></div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <!-- Top Bar -->
            <header class="dashboard-topbar">
                <div class="topbar-left">
                    <button class="mobile-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title"><?php View::yield('page_title', 'Dashboard'); ?></h1>
                </div>
                <?php
                    $merchantId = current_merchant_id();
                    $expiryTimestamp = time() + (86400 * 30);
                    if ($merchantId) {
                        $subRecord = db()->fetchOne("SELECT current_period_end FROM subscriptions WHERE merchant_id = ? ORDER BY id DESC LIMIT 1", [$merchantId]);
                        if (!empty($subRecord['current_period_end'])) {
                            $expiryTimestamp = strtotime($subRecord['current_period_end']);
                        }
                    }
                    $isPlanExpired = ($expiryTimestamp < time());
                ?>
                <div class="topbar-right d-flex align-items-center gap-2">
                    <!-- Real-time Live Countdown Pill -->
                    <div class="d-none d-md-flex align-items-center gap-2 px-3 py-1 bg-light border rounded-pill shadow-sm" title="Subscription Plan Expiration Countdown">
                        <i class="bi bi-clock-history text-danger"></i>
                        <span class="text-xs fw-700 text-dark font-monospace" id="liveCountdownText">Calculating...</span>
                    </div>

                    <?php if ($store): ?>
                    <a href="<?= url('store/' . $store['slug']) ?>" target="_blank" class="btn btn-sm btn-primary fw-700 d-flex align-items-center gap-1 shadow-sm px-3" style="border-radius: 8px;">
                        <i class="bi bi-globe"></i> View Site <i class="bi bi-arrow-up-right text-xs"></i>
                    </a>
                    <?php endif; ?>
                    <form method="POST" action="<?= url('logout') ?>" style="display:inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- Flash Messages -->
            <?php if (has_flash('success') || has_flash('error') || has_flash('warning')): ?>
            <div style="padding:1rem 2rem 0;">
                <?php if (has_flash('success')): ?>
                <div class="alert alert-success alert-dismissible animate-fade-in" role="alert">
                    <i class="bi bi-check-circle-fill alert-icon"></i>
                    <div><?= e(get_flash('success')) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                <?php if (has_flash('error')): ?>
                <div class="alert alert-danger alert-dismissible animate-fade-in" role="alert">
                    <i class="bi bi-exclamation-triangle-fill alert-icon"></i>
                    <div><?= e(get_flash('error')) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                <?php if (has_flash('warning')): ?>
                <div class="alert alert-warning alert-dismissible animate-fade-in" role="alert">
                    <i class="bi bi-exclamation-circle-fill alert-icon"></i>
                    <div><?= e(get_flash('warning')) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="dashboard-content">
                <?php View::yield('content'); ?>
            </div>
        </main>
    </div>

    <!-- Mandatory Expiry Modal Popup if Subscription Expired -->
    <?php if (!empty($isPlanExpired) && $isPlanExpired): ?>
    <div class="modal fade show d-block" id="expiredPlanModal" tabindex="-1" style="background: rgba(15,23,42,0.88); backdrop-filter: blur(10px); z-index: 99999;" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-body p-4 p-md-5 text-center">
                    <div class="rounded-circle bg-danger-subtle text-danger mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 76px; height: 76px; font-size: 2.25rem;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h3 class="fw-900 text-dark mb-2">Subscription Expired!</h3>
                    <p class="text-secondary small mb-4" style="line-height: 1.6;">
                        Aapka <strong>30-Day Store Subscription Plan</strong> expire ho chuka hai. Customer storefront temporarily offline hai. Apne store ko wapas live karne aur orders receive karne ke liye abhi plan renew karein.
                    </p>

                    <div class="bg-light p-3 rounded-3 mb-4 text-start border">
                        <div class="d-flex justify-content-between py-1 border-bottom small">
                            <span class="text-muted">Plan:</span>
                            <strong class="text-dark">BW Store All-Inclusive SaaS</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom small">
                            <span class="text-muted">Platform Commission:</span>
                            <strong class="text-success">0% FREE</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 align-items-center">
                            <span class="text-muted small">Renewal Fee:</span>
                            <strong class="text-primary fs-5">₹999 / month</strong>
                        </div>
                    </div>

                    <form method="POST" action="<?= url('dashboard/subscription/pay') ?>" data-loading>
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-800 shadow">
                            <i class="bi bi-lightning-charge-fill me-1"></i> Pay & Instant Reactivate Store
                        </button>
                    </form>

                    <div class="mt-3">
                        <form method="POST" action="<?= url('logout') ?>" style="display:inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-link text-muted btn-sm text-decoration-none">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout of account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= url('public/assets/js/app.js') ?>"></script>
    <script>
    (function() {
        var expiryTimestamp = <?= $expiryTimestamp ?>;
        function updateTimer() {
            var now = Math.floor(Date.now() / 1000);
            var distance = expiryTimestamp - now;
            if (distance <= 0) {
                var el = document.getElementById('liveCountdownText');
                if (el) el.innerHTML = '<span class="text-danger fw-800">EXPIRED</span>';
                var mainEl = document.getElementById('dashboardCountdownDisplay');
                if (mainEl) mainEl.innerHTML = '<span class="text-danger fw-800">00d 00h 00m 00s (EXPIRED)</span>';
                return;
            }
            var days = Math.floor(distance / (3600 * 24));
            var hours = Math.floor((distance % (3600 * 24)) / 3600);
            var minutes = Math.floor((distance % 3600) / 60);
            var seconds = Math.floor(distance % 60);

            var timeStr = days + "d " + (hours < 10 ? "0" + hours : hours) + "h " + (minutes < 10 ? "0" + minutes : minutes) + "m " + (seconds < 10 ? "0" + seconds : seconds) + "s";
            var el = document.getElementById('liveCountdownText');
            if (el) el.innerText = timeStr;
            var mainEl = document.getElementById('dashboardCountdownDisplay');
            if (mainEl) mainEl.innerText = timeStr;
        }
        updateTimer();
        setInterval(updateTimer, 1000);
    })();
    </script>
    <?php View::yield('scripts'); ?>
</body>
</html>
