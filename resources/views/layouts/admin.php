<?php use App\Core\View; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?php View::yield('title', 'Admin'); ?> — BW Store Admin</title>
    
    <!-- Google Fonts: Plus Jakarta Sans, Inter, JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('public/assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= url('public/assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= url('public/assets/css/admin.css') ?>">
</head>
<body>
    <div class="dashboard-wrapper">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Admin Sidebar (dark theme) -->
        <aside class="dashboard-sidebar admin-sidebar" id="dashboardSidebar">
            <div class="sidebar-header">
                <a href="<?= url('admin') ?>" class="bw-logo">
                    <div class="logo-mark shadow-sm">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 7H20L18.5 21H5.5L4 7Z" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8.5 7V4.5C8.5 3.11929 9.61929 2 11 2H13C14.3807 2 15.5 3.11929 15.5 4.5V7" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                            <path d="M9 13L11.5 15.5L15.5 10.5" stroke="#60A5FA" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="logo-text text-white">BW <span style="color:#60A5FA;">Store</span> <span class="admin-badge ms-1">ADMIN</span></div>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">Overview</div>
                <a href="<?= url('admin') ?>" class="nav-item <?= active_class('/admin') ?>">
                    <i class="bi bi-speedometer2 nav-icon"></i> Dashboard
                </a>

                <div class="nav-section">Manage</div>
                <a href="<?= url('admin/merchants') ?>" class="nav-item <?= active_class('/admin/merchants') ?>">
                    <i class="bi bi-shop nav-icon"></i> Merchants
                </a>
                <a href="<?= url('admin/stores') ?>" class="nav-item <?= active_class('/admin/stores') ?>">
                    <i class="bi bi-shop-window nav-icon"></i> Stores
                </a>
                <a href="<?= url('admin/subscriptions') ?>" class="nav-item <?= active_class('/admin/subscriptions') ?>">
                    <i class="bi bi-credit-card-2-back nav-icon"></i> Subscriptions (₹999/mo)
                </a>
                <a href="<?= url('admin/payments') ?>" class="nav-item <?= active_class('/admin/payments') ?>">
                    <i class="bi bi-cash-coin nav-icon"></i> Platform Billing
                </a>

                <div class="nav-section">System</div>
                <a href="<?= url('admin/support') ?>" class="nav-item <?= active_class('/admin/support') ?>">
                    <i class="bi bi-headset nav-icon"></i> Support Desk
                </a>
                <a href="<?= url('admin/announcements') ?>" class="nav-item <?= active_class('/admin/announcements') ?>">
                    <i class="bi bi-megaphone nav-icon"></i> Announcements
                </a>
                <a href="<?= url('admin/settings') ?>" class="nav-item <?= active_class('/admin/settings') ?>">
                    <i class="bi bi-gear nav-icon"></i> Settings
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="user-avatar" style="background:rgba(239,68,68,0.15);color:#FCA5A5;">
                        <?= strtoupper(substr(session()->get('user_name', 'A'), 0, 1)) ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?= e(session()->get('user_name', 'Admin')) ?></div>
                        <div class="user-email"><?= e(session()->get('user_email', '')) ?></div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-topbar">
                <div class="topbar-left">
                    <button class="mobile-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title"><?php View::yield('page_title', 'Admin Dashboard'); ?></h1>
                </div>
                <div class="topbar-right">
                    <form method="POST" action="<?= url('admin/logout') ?>" style="display:inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger px-3 fw-700 shadow-sm" style="border-radius: 8px;">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </header>

            <?php if (has_flash('success') || has_flash('error')): ?>
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
            </div>
            <?php endif; ?>

            <div class="dashboard-content">
                <?php View::yield('content'); ?>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= url('public/assets/js/app.js') ?>"></script>
    <?php View::yield('scripts'); ?>
</body>
</html>
