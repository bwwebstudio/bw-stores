<?php use App\Core\View; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?php View::yield('title', 'BW Store — Premium SaaS E-Commerce Platform'); ?></title>
    <meta name="description" content="<?php View::yield('description', 'Launch your brand online with BW Store SaaS. 3 high-converting themes, instant UPI QR & Razorpay, inventory tracking, and 0% sales commission starting at just ₹499/month.'); ?>">

    <!-- Google Fonts: Plus Jakarta Sans, Inter, JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Custom Design System CSS -->
    <link rel="stylesheet" href="<?= url('public/assets/css/app.css') ?>">
</head>
<body>

    <!-- Public Glass Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-public py-3">
        <div class="container">
            <a class="navbar-brand bw-logo" href="<?= url('/') ?>">
                <div class="logo-mark shadow">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 7H20L18.5 21H5.5L4 7Z" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8.5 7V4.5C8.5 3.11929 9.61929 2 11 2H13C14.3807 2 15.5 3.11929 15.5 4.5V7" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                        <path d="M9 13L11.5 15.5L15.5 10.5" stroke="#60A5FA" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="logo-text">
                    BW <span>Store</span>
                    <span class="logo-tag-saas ms-1">SAAS</span>
                </div>
            </a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-dark"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-600 gap-lg-1">
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= url('/#features') ?>">Features</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= url('/#themes') ?>">Themes</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= url('/#pricing') ?>">Pricing Plans</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= url('/#comparison') ?>">Why BW Store</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="<?= url('/#faq') ?>">FAQ</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    <?php if (is_authenticated()): ?>
                        <?php if (is_admin()): ?>
                            <a href="<?= url('admin') ?>" class="btn btn-dark btn-sm px-3 fw-700">
                                <i class="bi bi-shield-lock me-1"></i> Admin Portal
                            </a>
                        <?php else: ?>
                            <a href="<?= url('dashboard') ?>" class="btn btn-primary btn-sm px-3 fw-700 shadow-sm">
                                <i class="bi bi-grid-1x2-fill me-1"></i> Merchant Dashboard
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?= url('login') ?>" class="btn btn-outline-secondary btn-sm px-3 fw-600">
                            Merchant Login
                        </a>
                        <a href="<?= url('signup') ?>" class="btn btn-primary btn-sm px-4 fw-700 shadow-sm btn-glow">
                            Create Store <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main View Content -->
    <main>
        <?php View::yield('content'); ?>
    </main>

    <!-- Public Footer -->
    <footer class="py-5 bg-dark text-white border-top border-secondary">
        <div class="container pt-3">
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <div class="bw-logo mb-3">
                        <div class="logo-mark">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 7H20L18.5 21H5.5L4 7Z" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.5 7V4.5C8.5 3.11929 9.61929 2 11 2H13C14.3807 2 15.5 3.11929 15.5 4.5V7" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                                <path d="M9 13L11.5 15.5L15.5 10.5" stroke="#60A5FA" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="logo-text text-white">BW <span style="color:#60A5FA;">Store</span> <span class="logo-tag-saas ms-1">SAAS</span></div>
                    </div>
                    <p class="text-secondary small mb-4" style="max-width: 360px; line-height: 1.7;">
                        BW Store is a high-speed, multi-tenant subscription SaaS e-commerce platform built by <strong>BW Web Studio</strong>. Empowering independent brands with their own online stores from ₹499/month with 0% platform commission.
                    </p>
                    <div class="d-flex gap-3 text-secondary">
                        <span class="badge bg-secondary-subtle text-light border border-secondary px-3 py-2 text-xs">
                            <i class="bi bi-shield-check text-success me-1"></i> 256-Bit SSL Encrypted
                        </span>
                        <span class="badge bg-secondary-subtle text-light border border-secondary px-3 py-2 text-xs">
                            <i class="bi bi-lightning-charge-fill text-warning me-1"></i> 99.9% Cloud Uptime
                        </span>
                    </div>
                </div>
                
                <div class="col-lg-2 col-6">
                    <h6 class="text-white fw-700 mb-3 text-uppercase text-xs tracking-wider">Product Features</h6>
                    <ul class="list-unstyled text-secondary small">
                        <li class="mb-2"><a href="#features" class="text-secondary text-decoration-none hover-white">3 Premium Themes</a></li>
                        <li class="mb-2"><a href="#features" class="text-secondary text-decoration-none hover-white">UPI QR & Razorpay</a></li>
                        <li class="mb-2"><a href="#features" class="text-secondary text-decoration-none hover-white">Inventory Tracking</a></li>
                        <li class="mb-2"><a href="#features" class="text-secondary text-decoration-none hover-white">Coupon Engine</a></li>
                        <li class="mb-2"><a href="#pricing" class="text-secondary text-decoration-none hover-white">3-Tier Pricing</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-6">
                    <h6 class="text-white fw-700 mb-3 text-uppercase text-xs tracking-wider">SaaS Packages</h6>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 text-xs">
                            <li><a href="<?= url('/#pricing') ?>" class="footer-link">Starter (₹499/mo)</a></li>
                            <li><a href="<?= url('/#pricing') ?>" class="footer-link">Growth (₹999/mo)</a></li>
                            <li><a href="<?= url('/#pricing') ?>" class="footer-link">Enterprise (₹2,999/mo)</a></li>
                            <li><a href="<?= url('signup') ?>" class="footer-link text-primary fw-700">Start 7-Day Free Trial &rarr;</a></li>
                        </ul>
                </div>

                <div class="col-lg-2 col-6">
                    <h6 class="text-white fw-700 mb-3 text-uppercase text-xs tracking-wider">Portals & Login</h6>
                    <ul class="list-unstyled text-secondary small">
                        <li class="mb-2"><a href="<?= url('login') ?>" class="text-secondary text-decoration-none hover-white">Merchant Login</a></li>
                        <li class="mb-2"><a href="<?= url('admin/login') ?>" class="text-secondary text-decoration-none hover-white">Admin Portal</a></li>
                        <li class="mb-2"><a href="<?= url('signup') ?>" class="text-secondary text-decoration-none hover-white">Create New Store</a></li>
                        <li class="mb-2"><a href="#faq" class="text-secondary text-decoration-none hover-white">Help & Support</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-6">
                    <h6 class="text-white fw-700 mb-3 text-uppercase text-xs tracking-wider">Company</h6>
                    <p class="text-secondary small mb-1"><strong>Company:</strong> BW Web Studio</p>
                    <p class="text-secondary small mb-1"><strong>Product:</strong> BW Store SaaS</p>
                    <p class="text-secondary small mb-2"><strong>Support:</strong> 24/7 Ticket System</p>
                    <div class="text-xs text-muted">Made with passion for independent Indian merchants.</div>
                </div>
            </div>

            <div class="border-top border-secondary pt-4 d-flex flex-column flex-md-row justify-content-between align-items-center text-secondary text-xs gap-3">
                <div>
                    &copy; <?= date('Y') ?> <strong>BW Web Studio</strong>. All rights reserved.
                </div>
                <div class="d-flex gap-4">
                    <span><i class="bi bi-check-circle-fill text-success me-1"></i> 0% Platform Commission</span>
                    <span><i class="bi bi-check-circle-fill text-success me-1"></i> Direct Bank Payouts</span>
                    <span><i class="bi bi-check-circle-fill text-success me-1"></i> Instant Setup</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= url('public/assets/js/app.js') ?>"></script>
</body>
</html>
