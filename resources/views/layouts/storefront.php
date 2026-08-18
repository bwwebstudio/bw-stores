<?php use App\Core\View; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?php View::yield('title', e($store['name'])); ?> — <?= e($store['name']) ?></title>
    <meta name="description" content="<?php View::yield('description', e($store['description'] ?? 'Official online store.')); ?>">

    <?php if (!empty($store['favicon'])): ?>
        <link rel="icon" href="<?= url($store['favicon']) ?>">
    <?php endif; ?>

    <!-- Google Fonts: Plus Jakarta Sans, Inter, Playfair Display, JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- App Global Style -->
    <link rel="stylesheet" href="<?= url('public/assets/css/app.css') ?>">

    <!-- Dynamic Theme Styling based on Merchant Settings -->
    <style>
        :root {
            --store-primary: <?= e($settings['primary_color'] ?? '#2563EB') ?>;
            --store-secondary: <?= e($settings['secondary_color'] ?? '#0F172A') ?>;
            --store-font: <?= ($settings['theme'] ?? '') === 'fashion' ? "'Playfair Display', serif" : "'Plus Jakarta Sans', sans-serif" ?>;
        }

        body {
            font-family: var(--font-family);
            background-color: #FAFCFF;
            color: #0F172A;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--store-font);
        }

        /* Dynamic Brand Accents */
        .btn-store-primary {
            background: linear-gradient(135deg, var(--store-primary) 0%, var(--store-secondary) 100%);
            color: #FFFFFF !important;
            border: none;
            font-weight: 700;
            padding: 0.65rem 1.35rem;
            border-radius: var(--radius-md);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            transition: all var(--transition-fast);
        }
        .btn-store-primary:hover {
            opacity: 0.94;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
        }

        .text-store-primary { color: var(--store-primary) !important; }
        .bg-store-primary { background-color: var(--store-primary) !important; }

        /* Store Header Glassmorphic */
        .store-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid #E2E8F0;
            position: sticky;
            top: 0;
            z-index: 990;
        }

        /* Announcement Ribbon */
        .store-announcement-bar {
            background: #0F172A;
            color: #FFFFFF;
            font-size: 0.8125rem;
            font-weight: 600;
            padding: 0.45rem 1rem;
            text-align: center;
            letter-spacing: 0.02em;
        }

        /* Product Card Styles */
        .product-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            overflow: hidden;
            transition: all var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        }
        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.12);
            border-color: #CBD5E1;
        }
        .product-image-wrap {
            aspect-ratio: 1 / 1;
            background: #F8FAFC;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .product-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .product-card:hover .product-image-wrap img {
            transform: scale(1.06);
        }

        /* Floating WhatsApp Button */
        .whatsapp-float {
            position: fixed;
            bottom: 28px;
            right: 28px;
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            color: #FFFFFF;
            border-radius: 50%;
            width: 58px;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            box-shadow: 0 8px 24px rgba(37, 211, 102, 0.45);
            z-index: 1000;
            text-decoration: none;
            transition: all var(--transition-bounce);
        }
        .whatsapp-float:hover {
            transform: scale(1.12) translateY(-2px);
            color: #FFFFFF;
            box-shadow: 0 12px 28px rgba(37, 211, 102, 0.55);
        }

        /* Trust Ribbon Bar */
        .trust-ribbon {
            background: #FFFFFF;
            border-top: 1px solid #E2E8F0;
            border-bottom: 1px solid #E2E8F0;
            padding: 1.5rem 0;
        }
        .trust-item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }
        .trust-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #EFF6FF;
            color: #2563EB;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    <!-- Announcement Bar -->
    <div class="store-announcement-bar">
        <span>✨ Welcome to <strong><?= e($store['name']) ?></strong> &bull; Free Delivery Available &bull; 100% Direct UPI Payments</span>
    </div>

    <!-- Store Navigation Bar -->
    <header class="store-header py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="<?= url('store/' . $store['slug']) ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                <?php if (!empty($store['logo'])): ?>
                    <img src="<?= url($store['logo']) ?>" alt="<?= e($store['name']) ?>" style="max-height: 44px; border-radius: 8px;">
                <?php else: ?>
                    <span class="fs-4 fw-900 text-dark" style="letter-spacing: -0.03em;"><?= e($store['name']) ?></span>
                <?php endif; ?>
            </a>

            <!-- Navigation Links -->
            <nav class="d-none d-md-flex align-items-center gap-4 fw-700 small">
                <a href="<?= url('store/' . $store['slug']) ?>" class="text-dark text-decoration-none nav-link-custom">Home</a>
                <a href="<?= url('store/' . $store['slug'] . '/products') ?>" class="text-dark text-decoration-none nav-link-custom">All Products</a>
                <?php foreach (array_slice($categories ?? [], 0, 4) as $cat): ?>
                    <a href="<?= url('store/' . $store['slug'] . '/category/' . $cat['slug']) ?>" class="text-dark text-decoration-none nav-link-custom"><?= e($cat['name']) ?></a>
                <?php endforeach; ?>
            </nav>

            <!-- Search & Cart -->
            <div class="d-flex align-items-center gap-3">
                <form method="GET" action="<?= url('store/' . $store['slug'] . '/products') ?>" class="d-none d-lg-block" style="width: 220px;">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control rounded-start" placeholder="Search products..." style="border-color: #CBD5E1;">
                        <button type="submit" class="btn btn-outline-secondary" style="border-color: #CBD5E1;"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <a href="<?= url('store/' . $store['slug'] . '/cart') ?>" class="btn btn-store-primary position-relative btn-sm px-3 fw-800 d-inline-flex align-items-center gap-2">
                    <i class="bi bi-cart3"></i>
                    <span>Cart</span>
                    <span class="badge bg-danger rounded-pill"><?= $cartCount ?? 0 ?></span>
                </a>
            </div>
        </div>
    </header>

    <!-- Flash Messages on Storefront -->
    <?php if (has_flash('success') || has_flash('error') || has_flash('warning')): ?>
    <div class="container pt-3">
        <?php if (has_flash('success')): ?>
            <div class="alert alert-success alert-dismissible animate-fade-in" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= e(get_flash('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (has_flash('error')): ?>
            <div class="alert alert-danger alert-dismissible animate-fade-in" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= e(get_flash('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Store View Body -->
    <main>
        <?php View::yield('content'); ?>
    </main>

    <!-- Trust Ribbon -->
    <section class="trust-ribbon mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-6 col-lg-3">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="bi bi-truck"></i></div>
                        <div>
                            <div class="fw-800 text-dark small">Fast Shipping</div>
                            <div class="text-muted text-xs">Direct to your doorstep</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="trust-item">
                        <div class="trust-icon" style="background:#ECFDF5; color:#10B981;"><i class="bi bi-shield-check"></i></div>
                        <div>
                            <div class="fw-800 text-dark small">100% Safe Payments</div>
                            <div class="text-muted text-xs">Direct UPI & Razorpay</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="trust-item">
                        <div class="trust-icon" style="background:#FAF5FF; color:#7C3AED;"><i class="bi bi-arrow-repeat"></i></div>
                        <div>
                            <div class="fw-800 text-dark small">Quality Assured</div>
                            <div class="text-muted text-xs">Handcrafted standard</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="trust-item">
                        <div class="trust-icon" style="background:#FFFBEB; color:#F59E0B;"><i class="bi bi-whatsapp"></i></div>
                        <div>
                            <div class="fw-800 text-dark small">Instant Support</div>
                            <div class="text-muted text-xs">Chat directly on WhatsApp</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Floating WhatsApp Button if configured -->
    <?php if (!empty($settings['whatsapp_number'])): ?>
    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['whatsapp_number']) ?>?text=Hi%2C%20I%20am%20interested%20in%20your%20store%20products." class="whatsapp-float" target="_blank" aria-label="Chat on WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>
    <?php endif; ?>

    <!-- Store Footer -->
    <footer class="bg-dark text-white py-5 border-top border-secondary">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-5">
                    <h5 class="fw-900 text-white mb-2"><?= e($store['name']) ?></h5>
                    <p class="text-secondary small mb-3"><?= e($settings['hero_subtitle'] ?? 'Your trusted destination for premium products.') ?></p>
                    <?php if (!empty($settings['business_address'])): ?>
                        <p class="text-secondary small"><i class="bi bi-geo-alt me-1 text-primary"></i> <?= e($settings['business_address']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="col-lg-3 col-6">
                    <h6 class="text-white fw-800 mb-3">Quick Navigation</h6>
                    <ul class="list-unstyled text-secondary small">
                        <li class="mb-2"><a href="<?= url('store/' . $store['slug']) ?>" class="text-secondary text-decoration-none hover-white">Home</a></li>
                        <li class="mb-2"><a href="<?= url('store/' . $store['slug'] . '/products') ?>" class="text-secondary text-decoration-none hover-white">Catalog</a></li>
                        <li class="mb-2"><a href="<?= url('store/' . $store['slug'] . '/cart') ?>" class="text-secondary text-decoration-none hover-white">Shopping Cart</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-6">
                    <h6 class="text-white fw-800 mb-3">Customer Care</h6>
                    <?php if (!empty($settings['contact_email'])): ?>
                        <div class="small text-secondary mb-2"><i class="bi bi-envelope me-1 text-primary"></i> <?= e($settings['contact_email']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($settings['whatsapp_number'])): ?>
                        <div class="small text-secondary mb-3"><i class="bi bi-whatsapp me-1 text-success"></i> <?= e($settings['whatsapp_number']) ?></div>
                    <?php endif; ?>

                    <div class="d-flex gap-2 mt-3">
                        <?php if (!empty($settings['instagram_url'])): ?>
                            <a href="<?= e($settings['instagram_url']) ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['facebook_url'])): ?>
                            <a href="<?= e($settings['facebook_url']) ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-facebook"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="border-top border-secondary pt-3 d-flex justify-content-between align-items-center flex-wrap small text-secondary">
                <div>&copy; <?= date('Y') ?> <?= e($store['name']) ?>. <?= e($settings['footer_text'] ?? 'All rights reserved.') ?></div>
                <div>Powered by <a href="<?= url('/') ?>" class="text-white text-decoration-none fw-700">BW Store SaaS</a></div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= url('public/assets/js/app.js') ?>"></script>
</body>
</html>
