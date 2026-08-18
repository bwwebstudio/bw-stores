<?php use App\Core\View; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?php View::yield('title', 'BW Store'); ?> — BW Store</title>
    <meta name="description" content="<?php View::yield('description', 'Your Store. Your Brand. Your Sales. Create your online store with BW Store.'); ?>">
    
    <!-- Google Fonts: Plus Jakarta Sans, Inter, JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- App Styles -->
    <link rel="stylesheet" href="<?= url('public/assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= url('public/assets/css/auth.css') ?>">
</head>
<body>
    <!-- Flash Messages -->
    <?php if (has_flash('success') || has_flash('error') || has_flash('warning')): ?>
    <div style="position:fixed;top:1rem;right:1rem;z-index:9999;max-width:400px;width:100%;">
        <?php if (has_flash('success')): ?>
        <div class="alert alert-success alert-dismissible animate-fade-in" role="alert">
            <i class="bi bi-check-circle-fill alert-icon"></i>
            <div><?= e(get_flash('success')) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        <?php if (has_flash('error')): ?>
        <div class="alert alert-danger alert-dismissible animate-fade-in" role="alert">
            <i class="bi bi-exclamation-triangle-fill alert-icon"></i>
            <div><?= e(get_flash('error')) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        <?php if (has_flash('warning')): ?>
        <div class="alert alert-warning alert-dismissible animate-fade-in" role="alert">
            <i class="bi bi-exclamation-circle-fill alert-icon"></i>
            <div><?= e(get_flash('warning')) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="auth-wrapper">
        <!-- Left Brand Panel -->
        <div class="auth-brand">
            <a href="<?= url('/') ?>" class="bw-logo mb-4" style="text-decoration:none;">
                <div class="logo-mark shadow">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 7H20L18.5 21H5.5L4 7Z" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8.5 7V4.5C8.5 3.11929 9.61929 2 11 2H13C14.3807 2 15.5 3.11929 15.5 4.5V7" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                        <path d="M9 13L11.5 15.5L15.5 10.5" stroke="#60A5FA" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="logo-text" style="color:#fff;">BW <span>Store</span> <span class="logo-tag-saas ms-1">SAAS</span></div>
            </a>

            <h1 class="brand-tagline">
                Your Store.<br>
                Your Brand.<br>
                <span>Zero Commission.</span>
            </h1>

            <p class="brand-description">
                Create and scale your independent online store with BW Store. Start with a <strong>7-Day Free Trial</strong> and pay as low as ₹499/mo!
            </p>

            <ul class="brand-features">
                <li>
                    <span class="feature-icon"><i class="bi bi-gift-fill text-warning"></i></span>
                    Includes 7-Day Full Free Trial
                </li>
                <li>
                    <span class="feature-icon"><i class="bi bi-check-lg text-success"></i></span>
                    Plans from ₹499/month — 0% Platform Commission
                </li>
                <li>
                    <span class="feature-icon"><i class="bi bi-check-lg text-success"></i></span>
                    3 High-Speed Themes (Modern, Fashion, Business)
                </li>
                <li>
                    <span class="feature-icon"><i class="bi bi-check-lg text-success"></i></span>
                    Direct Merchant UPI QR + Razorpay Payments
                </li>
                <li>
                    <span class="feature-icon"><i class="bi bi-check-lg text-success"></i></span>
                    Built-in Real-Time Analytics & Inventory Tracking
                </li>
            </ul>
        </div>

        <!-- Right Form Panel -->
        <div class="auth-form-panel">
            <?php View::yield('content'); ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= url('public/assets/js/app.js') ?>"></script>
</body>
</html>
