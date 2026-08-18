<?php

use App\Core\View;
View::layout('layouts.app');

View::section('title'); ?>BW Store — Launch Your Online Store with 7-Day Free Trial<?php View::endSection();
View::section('description'); ?>Build and scale your independent e-commerce store from ₹499/mo with a 7-Day Free Trial. 0% platform commission, 3 premium storefront themes, instant UPI QR & Razorpay integration.<?php View::endSection();

View::section('content'); ?>

<!-- ==========================================
     HERO SECTION: High-Converting Dark SaaS Hero
     ========================================== -->
<section class="hero-wrapper position-relative text-center">
    <div class="hero-glow-bg"></div>
    <div class="hero-grid-pattern"></div>

    <div class="container position-relative" style="z-index: 3;">
        <!-- Pill Announcement Badge with 7-Day Trial -->
        <div class="mb-4">
            <div class="hero-pill-badge">
                <i class="bi bi-gift-fill text-warning"></i> 
                <span>Start with 7-Day Free Trial &bull; 0% Platform Commission</span>
            </div>
        </div>

        <!-- Main Headline -->
        <h1 class="hero-title-main">
            Your Store. Your Brand.<br>
            <span class="gradient-text">Zero Platform Commission.</span>
        </h1>

        <!-- Subtitle -->
        <p class="hero-subtitle">
            Launch a blazing-fast, professional online store with zero code in under 5 minutes. Accept direct UPI & Card payments into your own bank account — starting at just ₹499/month with a <strong>7-Day Free Trial</strong>.
        </p>

        <!-- CTA Action Buttons -->
        <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3 mb-4">
            <a href="<?= url('signup') ?>" class="btn btn-primary-gradient btn-lg px-5 py-3 shadow-lg fw-700 btn-glow">
                <i class="bi bi-rocket-takeoff-fill me-2"></i> Start 7-Day Free Trial Now
            </a>
            <a href="#pricing" class="btn btn-outline-light btn-lg px-4 py-3 fw-600 rounded-3">
                <i class="bi bi-tag-fill text-primary me-2"></i> View Pricing (From ₹499/mo)
            </a>
        </div>

        <!-- Trust Badges Under CTA -->
        <div class="d-flex flex-wrap justify-content-center gap-4 text-white text-xs mt-2" style="color: #E2E8F0 !important;">
            <span><i class="bi bi-check-circle-fill text-success me-1"></i> 7-Day Free Trial Included</span>
            <span><i class="bi bi-check-circle-fill text-success me-1"></i> 0% Platform Sales Cut</span>
            <span><i class="bi bi-check-circle-fill text-success me-1"></i> Instant UPI QR & Razorpay</span>
            <span><i class="bi bi-check-circle-fill text-success me-1"></i> 3 Premium Themes</span>
        </div>

        <!-- Interactive Live Storefront Browser Mockup -->
        <div class="hero-mockup-frame text-start mt-5">
            <div class="mockup-header-bar">
                <div class="mockup-dots">
                    <div class="mockup-dot dot-red"></div>
                    <div class="mockup-dot dot-yellow"></div>
                    <div class="mockup-dot dot-green"></div>
                </div>
                <div class="mockup-url-bar">
                    <i class="bi bi-lock-fill text-success"></i>
                    <span>https://bwstore.in/store/royalsilk</span>
                </div>
                <div class="text-xs text-white d-none d-sm-block">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">LIVE STOREFRONT</span>
                </div>
            </div>

            <div class="mockup-content-grid">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <div class="rounded-3 p-3 text-center position-relative" style="background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); border: 1px solid rgba(255,255,255,0.15);">
                            <div class="badge bg-primary position-absolute top-0 start-0 m-3 px-2 py-1 text-xs">BESTSELLER</div>
                            <div class="py-4 text-center">
                                <i class="bi bi-handbag text-white" style="font-size: 5rem; opacity: 0.95;"></i>
                            </div>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <span class="badge bg-dark border border-secondary text-white px-2 py-1 text-xs">S</span>
                                <span class="badge bg-primary text-white px-2 py-1 text-xs">M</span>
                                <span class="badge bg-dark border border-secondary text-white px-2 py-1 text-xs">L</span>
                                <span class="badge bg-dark border border-secondary text-white px-2 py-1 text-xs">XL</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 text-white">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-warning text-dark fw-800 text-xs"><i class="bi bi-star-fill me-1"></i> 4.9 (128 reviews)</span>
                            <span class="text-white text-xs opacity-75">In Stock &bull; Ships in 24h</span>
                        </div>
                        <h4 class="fw-800 text-white mb-2">Artisan Handcrafted Pure Silk Kurta</h4>
                        <p class="text-white text-xs mb-3 opacity-75">
                            Premium hand-woven organic festive edition with zari border detailing and comfort lining.
                        </p>
                        <div class="d-flex align-items-baseline gap-3 mb-3">
                            <span class="fs-3 fw-900 text-white">₹2,499</span>
                            <span class="text-white text-decoration-line-through text-xs opacity-50">₹3,999</span>
                            <span class="badge bg-success-subtle text-success text-xs font-monospace">SAVE 38%</span>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <div class="btn btn-success btn-sm fw-700 px-3 py-2 d-flex align-items-center gap-2">
                                <i class="bi bi-qr-code-scan"></i> Pay via Instant UPI QR
                            </div>
                            <div class="btn btn-outline-light btn-sm fw-600 px-3 py-2">
                                <i class="bi bi-credit-card-2-front"></i> Razorpay / Cards
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating Live Order Toast -->
            <div class="live-sales-toast">
                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-bag-check-fill"></i>
                </div>
                <div>
                    <div class="fw-700 text-white">New Order #BW-9482</div>
                    <div class="text-white text-xs opacity-75">Pooja from Pune just ordered &bull; 2 mins ago</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     STATISTICS TICKER BAR
     ========================================== -->
<section class="stats-ticker-bar">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="stat-metric-value">0%</div>
                <div class="stat-metric-label">Sales Commission Cut</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-metric-value">&lt; 5 Min</div>
                <div class="stat-metric-label">Instant Store Setup</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-metric-value">3 Themes</div>
                <div class="stat-metric-label">Modern, Fashion & Business</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-metric-value">7 Days</div>
                <div class="stat-metric-label">Free Trial On All Signups</div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     CORE FEATURES SECTION (Engineered to Sell)
     ========================================== -->
<section class="py-5 bg-white" id="features">
    <div class="container py-5">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-800 text-uppercase tracking-wider text-xs mb-2">Everything You Need</span>
            <h2 class="display-6 fw-900 mt-2 mb-3 text-dark">Built for High Conversions & Zero Headaches</h2>
            <p class="text-secondary lead fs-6">BW Store gives you all the tools of an enterprise e-commerce stack without high fees, complex liquid coding, or commission cuts.</p>
        </div>

        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-4">
                <div class="feature-box-premium">
                    <div class="feature-icon-gradient">
                        <i class="bi bi-palette-fill"></i>
                    </div>
                    <h5 class="fw-800 mb-2 text-dark">3 High-Conversion Themes</h5>
                    <p class="text-secondary small mb-0">Choose between Modern Retail, Luxury Fashion Boutique, or Business B2B themes. Fully responsive on mobile, tablet, and desktop.</p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="col-md-4">
                <div class="feature-box-premium">
                    <div class="feature-icon-gradient">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <h5 class="fw-800 mb-2 text-dark">Direct UPI & Razorpay Connect</h5>
                    <p class="text-secondary small mb-0">Customer funds never touch our account. Accept payments via dynamic UPI QR (GPay, PhonePe, Paytm), Razorpay Gateway, or Cash on Delivery.</p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="col-md-4">
                <div class="feature-box-premium">
                    <div class="feature-icon-gradient">
                        <i class="bi bi-boxes"></i>
                    </div>
                    <h5 class="fw-800 mb-2 text-dark">SKU Variants & Stock Alerts</h5>
                    <p class="text-secondary small mb-0">Manage sizes, colors, inventory alerts, and automatic stock deduction with complete inventory audit ledger.</p>
                </div>
            </div>

            <!-- Feature 4 -->
            <div class="col-md-4">
                <div class="feature-box-premium">
                    <div class="feature-icon-gradient">
                        <i class="bi bi-ticket-perforated-fill"></i>
                    </div>
                    <h5 class="fw-800 mb-2 text-dark">Coupons & Flash Discounts</h5>
                    <p class="text-secondary small mb-0">Create percentage discounts, fixed rupee value coupon codes, min order limits, and expiry dates to boost average order value.</p>
                </div>
            </div>

            <!-- Feature 5 -->
            <div class="col-md-4">
                <div class="feature-box-premium">
                    <div class="feature-icon-gradient">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h5 class="fw-800 mb-2 text-dark">Real-Time Sales Analytics</h5>
                    <p class="text-secondary small mb-0">Track total revenue, average order value, pending shipments, customer database, and top selling products from your dashboard.</p>
                </div>
            </div>

            <!-- Feature 6 -->
            <div class="col-md-4">
                <div class="feature-box-premium">
                    <div class="feature-icon-gradient">
                        <i class="bi bi-whatsapp"></i>
                    </div>
                    <h5 class="fw-800 mb-2 text-dark">WhatsApp Order Management</h5>
                    <p class="text-secondary small mb-0">1-click WhatsApp order confirmation, floating WhatsApp support widget on your storefront, and instant printable PDF invoices.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     THEMES SHOWCASE SECTION
     ========================================== -->
<section class="py-5 bg-light border-top border-bottom" id="themes">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-800 text-uppercase tracking-wider text-xs mb-2">Storefront Designs</span>
            <h2 class="display-6 fw-900 mt-2 mb-3 text-dark">Engineered for Every Industry</h2>
            <p class="text-secondary lead fs-6">Every theme is speed-optimized, SEO-ready, and designed to look stunning on mobile screens.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="p-4 bg-primary text-white text-center" style="min-height: 160px; display: flex; flex-direction: column; justify-content: center;">
                        <span class="badge bg-white text-primary fw-800 text-xs mb-2 align-self-center">DEFAULT THEME</span>
                        <h4 class="fw-800 mb-1 text-white">Modern Theme</h4>
                        <div class="text-white text-xs opacity-75">For Gadgets, Essentials & General Retail</div>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-secondary small mb-3">Clean card layouts, high-visibility add-to-cart buttons, fast search filters, and sticky checkout footer.</p>
                        <div class="d-flex justify-content-between align-items-center text-xs text-secondary border-top pt-3">
                            <span><i class="bi bi-lightning-fill text-warning me-1"></i> Blazing Fast Load</span>
                            <span class="fw-700 text-primary">Included in All Plans</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="p-4 text-white text-center" style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); min-height: 160px; display: flex; flex-direction: column; justify-content: center;">
                        <span class="badge bg-white text-dark fw-800 text-xs mb-2 align-self-center">RECOMMENDED</span>
                        <h4 class="fw-800 mb-1 text-white">Fashion Boutique</h4>
                        <div class="text-white text-xs opacity-75">For Apparel, Jewelry, Boutiques & Beauty</div>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-secondary small mb-3">High-fashion editorial typography, lookbook carousels, size selection pills, and customer testimonial quotes.</p>
                        <div class="d-flex justify-content-between align-items-center text-xs text-secondary border-top pt-3">
                            <span><i class="bi bi-eye-fill text-primary me-1"></i> Luxury Look & Feel</span>
                            <span class="fw-700 text-primary">Growth & Enterprise</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="p-4 bg-dark text-white text-center" style="min-height: 160px; display: flex; flex-direction: column; justify-content: center;">
                        <span class="badge bg-secondary text-white fw-800 text-xs mb-2 align-self-center">PROFESSIONAL</span>
                        <h4 class="fw-800 mb-1 text-white">Business B2B</h4>
                        <div class="text-white text-xs opacity-75">For Electronics, Hardware & B2B Catalogs</div>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-secondary small mb-3">Structured product specification tables, SKU tags, clear inventory counters, and direct enquiry actions.</p>
                        <div class="d-flex justify-content-between align-items-center text-xs text-secondary border-top pt-3">
                            <span><i class="bi bi-shield-fill-check text-success me-1"></i> Enterprise Grade</span>
                            <span class="fw-700 text-primary">Growth & Enterprise</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     SHOPIFY VS BW STORE COMPARISON TABLE
     ========================================== -->
<section class="py-5 bg-white" id="comparison">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-800 text-uppercase tracking-wider text-xs mb-2">Cost & Performance Comparison</span>
            <h2 class="display-6 fw-900 mt-2 mb-3 text-dark">Why Smart Merchants Choose BW Store</h2>
            <p class="text-secondary lead fs-6">Compare what you pay and what you get versus international platforms like Shopify.</p>
        </div>

        <div class="comparison-table-wrapper">
            <div class="table-responsive">
                <table class="table comparison-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 38%;" class="fs-6">Platform Feature</th>
                            <th style="width: 31%; background: #EFF6FF; color: #1E40AF;" class="fs-6 text-center">
                                <div class="fw-900 text-primary fs-5">BW Store SaaS</div>
                                <span class="badge bg-primary text-white text-xs">RECOMMENDED</span>
                            </th>
                            <th style="width: 31%;" class="fs-6 text-center text-dark">Shopify</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-700 text-dark">Free Trial Access</td>
                            <td class="text-center fw-800 text-success bg-primary-50">
                                <i class="bi bi-check-circle-fill me-1"></i> 7-Day Full Free Trial
                            </td>
                            <td class="text-center text-secondary fw-600">3 Days only</td>
                        </tr>
                        <tr>
                            <td class="fw-700 text-dark">Monthly Platform Cost</td>
                            <td class="text-center fw-800 text-success bg-primary-50">₹499 &bull; ₹999 &bull; ₹2,999 / mo</td>
                            <td class="text-center text-danger fw-600">~ ₹3,300+ / mo ($39/mo)</td>
                        </tr>
                        <tr>
                            <td class="fw-700 text-dark">Platform Sales Commission</td>
                            <td class="text-center fw-800 text-success bg-primary-50">
                                <i class="bi bi-check-circle-fill me-1"></i> 0% (You keep 100% Profit)
                            </td>
                            <td class="text-center text-danger fw-600">2.0% cut on every customer sale</td>
                        </tr>
                        <tr>
                            <td class="fw-700 text-dark">Indian Native UPI QR Integration</td>
                            <td class="text-center fw-700 text-dark bg-primary-50">
                                <i class="bi bi-check-circle-fill text-success me-1"></i> Built-in Direct UPI QR
                            </td>
                            <td class="text-center text-secondary">Requires complex external apps</td>
                        </tr>
                        <tr>
                            <td class="fw-700 text-dark">Razorpay Gateway 1-Click Connect</td>
                            <td class="text-center fw-700 text-dark bg-primary-50">
                                <i class="bi bi-check-circle-fill text-success me-1"></i> 1-Click Keys Connect
                            </td>
                            <td class="text-center text-secondary">Extra third-party app fees</td>
                        </tr>
                        <tr>
                            <td class="fw-700 text-dark">Setup Time & Complexity</td>
                            <td class="text-center fw-700 text-dark bg-primary-50">
                                <span class="badge bg-success-subtle text-success">5 Minutes &bull; Zero Code</span>
                            </td>
                            <td class="text-center text-secondary">Requires Liquid coding / dev agency</td>
                        </tr>
                        <tr>
                            <td class="fw-700 text-dark">WhatsApp Sharing & Support Widget</td>
                            <td class="text-center fw-700 text-dark bg-primary-50">
                                <i class="bi bi-check-circle-fill text-success me-1"></i> Free Built-in
                            </td>
                            <td class="text-center text-secondary">Paid monthly Shopify app</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     TRANSPARENT 3-TIER PRICING MATRIX WITH MONTHLY/YEARLY TOGGLE
     ========================================== -->
<section class="pricing-section-wrapper" id="pricing">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-4">
            <span class="badge bg-primary text-white px-3 py-2 rounded-pill fw-800 text-uppercase tracking-wider text-xs mb-2">Transparent Pricing</span>
            <h2 class="display-5 fw-900 mt-2 mb-3 text-dark">Simple Plans. Massive Value.</h2>
            <p class="text-secondary lead fs-6">Every plan starts with a <strong>7-Day Free Trial</strong>. Pick monthly or save big with yearly billing!</p>
            
            <!-- Monthly / Yearly Switch Toggle -->
            <div class="billing-toggle-wrapper mt-3">
                <button type="button" class="billing-toggle-btn active" id="btnMonthly" onclick="toggleBilling('monthly')">Monthly Billing</button>
                <button type="button" class="billing-toggle-btn" id="btnYearly" onclick="toggleBilling('yearly')">
                    Yearly Billing <span class="badge bg-warning text-dark fw-800 text-xs ms-1">Save up to ₹1,000 Flat</span>
                </button>
            </div>
        </div>

        <div class="row g-4 justify-content-center align-items-stretch">
            <!-- PLAN 1: STARTER (₹499) -->
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card-tier">
                    <div class="badge bg-secondary-subtle text-dark border align-self-start mb-2 px-3 py-1 text-xs fw-800">STARTER</div>
                    <div class="mb-3">
                        <h3 class="fw-900 text-dark mb-1">BW Store Starter</h3>
                        <p class="text-secondary small mb-0" style="min-height: 42px;">Ideal for new sellers & creators starting their online boutique journey.</p>
                    </div>

                    <!-- Price Tag -->
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="tier-price-amount price-starter">₹499</span>
                        <span class="text-secondary ms-2 fw-700 period-text-starter">/ month</span>
                    </div>
                    <div class="text-xs text-success fw-700 mb-3 discount-starter" style="display: none;">
                        <i class="bi bi-tags-fill me-1"></i> Save ₹100 Flat (₹5,888 billed yearly)
                    </div>

                    <div class="badge bg-success-subtle text-success border border-success-subtle py-2 px-3 mb-3 fw-700 text-xs">
                        <i class="bi bi-gift-fill me-1"></i> Includes 7-Day Free Trial
                    </div>

                    <!-- Feature List -->
                    <ul class="list-unstyled mb-4 flex-grow-1">
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>0% Platform Commission</strong></span>
                        </li>
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>Up to <strong>10 Products</strong></span>
                        </li>
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>Modern High-Speed Storefront Theme</span>
                        </li>
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>Direct Merchant UPI + COD Ready</span>
                        </li>
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>Standard Analytics & Insights</span>
                        </li>
                        <li class="tier-feature-item">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>Free SSL & Subdomain Hosting</span>
                        </li>
                    </ul>

                    <!-- CTA Button -->
                    <a href="<?= url('signup?plan=starter') ?>" class="btn btn-outline-primary btn-lg w-100 fw-700 py-3 rounded-3 shadow-sm">
                        Start 7-Day Free Trial &rarr;
                    </a>
                </div>
            </div>

            <!-- PLAN 2: GROWTH (₹999 - RECOMMENDED) -->
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card-tier tier-popular">
                    <div class="tier-badge-pill">RECOMMENDED</div>
                    <div class="mb-3">
                        <h3 class="fw-900 text-dark mb-1">BW Store Growth</h3>
                        <p class="text-secondary small mb-0" style="min-height: 42px;">The complete powerhouse solution for ambitious brands scaling revenue.</p>
                    </div>

                    <!-- Price Tag -->
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="tier-price-amount price-growth">₹999</span>
                        <span class="text-secondary ms-2 fw-700 period-text-growth">/ month</span>
                    </div>
                    <div class="text-xs text-success fw-700 mb-3 discount-growth" style="display: none;">
                        <i class="bi bi-tags-fill me-1"></i> Save ₹200 Flat (₹11,788 billed yearly)
                    </div>

                    <div class="badge bg-success-subtle text-success border border-success-subtle py-2 px-3 mb-3 fw-700 text-xs">
                        <i class="bi bi-gift-fill me-1"></i> Includes 7-Day Free Trial
                    </div>

                    <!-- Feature List -->
                    <ul class="list-unstyled mb-4 flex-grow-1">
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>0% Platform Commission</strong></span>
                        </li>
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>Unlimited Products & Categories</strong></span>
                        </li>
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span><strong>All 3 Themes</strong> (Modern, Fashion, Business)</span>
                        </li>
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>Direct Razorpay Connect + UPI + COD</span>
                        </li>
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>Advanced Real-Time Sales Analytics</span>
                        </li>
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>Coupons & Dynamic Discount Engine</span>
                        </li>
                        <li class="tier-feature-item">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>Priority Email & Ticket Support</span>
                        </li>
                    </ul>

                    <!-- CTA Button -->
                    <a href="<?= url('signup?plan=growth') ?>" class="btn btn-primary-gradient btn-glow btn-lg w-100 fw-700 py-3 rounded-3 shadow">
                        Start 7-Day Free Trial &rarr;
                    </a>
                </div>
            </div>

            <!-- PLAN 3: ENTERPRISE (₹2,999 - VIP) -->
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card-tier tier-enterprise">
                    <div class="badge bg-purple text-white align-self-start mb-2 px-3 py-1 text-xs fw-800" style="background: #7C3AED;">VIP BUSINESS</div>
                    <div class="mb-3">
                        <h3 class="fw-900 text-dark mb-1">BW Store Enterprise</h3>
                        <p class="text-secondary small mb-0" style="min-height: 42px;">Engineered for established stores & high-volume merchants demanding VIP performance.</p>
                    </div>

                    <!-- Price Tag -->
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="tier-price-amount price-enterprise">₹2,999</span>
                        <span class="text-secondary ms-2 fw-700 period-text-enterprise">/ month</span>
                    </div>
                    <div class="text-xs text-success fw-700 mb-3 discount-enterprise" style="display: none;">
                        <i class="bi bi-tags-fill me-1"></i> Save ₹1,000 Flat (₹34,988 billed yearly)
                    </div>

                    <div class="badge bg-success-subtle text-success border border-success-subtle py-2 px-3 mb-3 fw-700 text-xs">
                        <i class="bi bi-gift-fill me-1"></i> Includes 7-Day Free Trial
                    </div>

                    <!-- Feature List -->
                    <ul class="list-unstyled mb-4 flex-grow-1">
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>0% Platform Commission</strong></span>
                        </li>
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><strong>Unlimited Products & Unlimited Traffic</strong></span>
                        </li>
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>All Themes + <strong>Custom CSS & Branding</strong></span>
                        </li>
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span><strong>Custom Domain DNS Mapping Ready</strong></span>
                        </li>
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span><strong>Dedicated VIP 24/7 WhatsApp & Ticket Support</strong></span>
                        </li>
                        <li class="tier-feature-item border-bottom">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>All Payment Gateways + Instant Settlements</span>
                        </li>
                        <li class="tier-feature-item">
                            <i class="bi bi-check-circle-fill text-primary"></i>
                            <span>Dedicated Account Onboarding Assistance</span>
                        </li>
                    </ul>

                    <!-- CTA Button -->
                    <a href="<?= url('signup?plan=enterprise') ?>" class="btn btn-dark btn-lg w-100 fw-700 py-3 rounded-3 shadow-sm">
                        Start 7-Day Free Trial &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     HOW IT WORKS (4 Simple Steps)
     ========================================== -->
<section class="py-5 bg-white border-top border-bottom" id="how-it-works">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-800 text-uppercase tracking-wider text-xs mb-2">Effortless Onboarding</span>
            <h2 class="display-6 fw-900 mt-2 mb-3 text-dark">Live in 4 Simple Steps</h2>
            <p class="text-secondary lead fs-6">From initial registration to accepting live payments in under 5 minutes with our 7-Day Free Trial.</p>
        </div>

        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="rounded-circle bg-primary text-white mx-auto d-flex align-items-center justify-content-center mb-3 shadow" style="width: 60px; height: 60px; font-weight: 800; font-size: 1.5rem;">1</div>
                    <h5 class="fw-800 text-dark">1. Sign Up Free</h5>
                    <p class="text-secondary small mb-0">Create your account and instantly activate your 7-day free trial.</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="rounded-circle bg-primary text-white mx-auto d-flex align-items-center justify-content-center mb-3 shadow" style="width: 60px; height: 60px; font-weight: 800; font-size: 1.5rem;">2</div>
                    <h5 class="fw-800 text-dark">2. Pick Theme</h5>
                    <p class="text-secondary small mb-0">Choose Modern, Fashion, or Business theme and set your colors.</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="rounded-circle bg-primary text-white mx-auto d-flex align-items-center justify-content-center mb-3 shadow" style="width: 60px; height: 60px; font-weight: 800; font-size: 1.5rem;">3</div>
                    <h5 class="fw-800 text-dark">3. Connect UPI / Cards</h5>
                    <p class="text-secondary small mb-0">Input your UPI ID or Razorpay keys for direct customer settlements.</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="rounded-circle bg-primary text-white mx-auto d-flex align-items-center justify-content-center mb-3 shadow" style="width: 60px; height: 60px; font-weight: 800; font-size: 1.5rem;">4</div>
                    <h5 class="fw-800 text-dark">4. Go Live Worldwide</h5>
                    <p class="text-secondary small mb-0">Start sharing your store link and accepting live orders immediately!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     FREQUENTLY ASKED QUESTIONS (FAQ)
     ========================================== -->
<section class="py-5 bg-light" id="faq">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-800 text-uppercase tracking-wider text-xs mb-2">Got Questions?</span>
            <h2 class="display-6 fw-900 mt-2 mb-3 text-dark">Frequently Asked Questions</h2>
            <p class="text-secondary lead fs-6">Everything you need to know about setting up and running your BW Store.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion shadow-sm rounded-4 overflow-hidden border" id="faqAccordion">
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button fw-800 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                How does the 7-Day Free Trial work?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary small">
                                When you sign up, your account is immediately activated with a <strong>7-Day Free Trial on our Growth Plan</strong>. You can upload products, connect your payment methods, customize your theme, and accept real customer orders. After 7 days, you can choose whichever plan suits your store (Starter ₹499, Growth ₹999, or Enterprise ₹2,999).
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed fw-800 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                What discount do I get on Yearly Plans?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary small">
                                When you choose yearly billing:
                                <ul>
                                    <li><strong>Enterprise Plan:</strong> Enjoy a <strong>₹1,000 Flat discount</strong> on your yearly total (₹34,988/yr instead of ₹35,988).</li>
                                    <li><strong>Growth Plan:</strong> Enjoy a <strong>₹200 Flat discount</strong> on your yearly total (₹11,788/yr instead of ₹11,988).</li>
                                    <li><strong>Starter Plan:</strong> Enjoy a <strong>₹100 Flat discount</strong> on your yearly total (₹5,888/yr instead of ₹5,988).</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed fw-800 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Do you charge any sales commission?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary small">
                                <strong>No, absolutely zero (0%) commission!</strong> You keep 100% of your profits. You only pay the flat subscription price.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed fw-800 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                How do my customers pay me?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary small">
                                Customers can pay via <strong>Direct Dynamic UPI QR Code</strong> (Google Pay, PhonePe, Paytm, BHIM), <strong>Razorpay Payment Gateway</strong> (Cards, NetBanking, Wallets), or <strong>Cash on Delivery (COD)</strong>. Funds flow directly into your own bank account.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     HIGH-CONVERTING BOTTOM CTA BANNER
     ========================================== -->
<section class="py-5 text-center text-white position-relative" style="background: #0B0F19;">
    <div class="container py-5 position-relative" style="z-index: 2;">
        <h2 class="display-5 fw-900 mb-3 text-white">Ready to Build Your Digital Storefront?</h2>
        <p class="text-white lead fs-6 mb-4 max-w-700 mx-auto opacity-75" style="max-width: 600px;">
            Join smart Indian merchants scaling their revenue with 0% commission cuts, 7-day free trial, and full brand autonomy.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= url('signup') ?>" class="btn btn-primary-gradient btn-lg px-5 py-3 fw-800 shadow-lg btn-glow">
                Start 7-Day Free Trial Now <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>

<script>
function toggleBilling(type) {
    var btnM = document.getElementById('btnMonthly');
    var btnY = document.getElementById('btnYearly');
    
    if (type === 'yearly') {
        btnY.classList.add('active');
        btnM.classList.remove('active');
        
        // Update Prices to Flat Yearly Totals
        document.querySelector('.price-starter').innerText = '₹5,888';
        document.querySelector('.price-growth').innerText = '₹11,788';
        document.querySelector('.price-enterprise').innerText = '₹34,988';

        document.querySelector('.period-text-starter').innerText = '/ 1 Year';
        document.querySelector('.period-text-growth').innerText = '/ 1 Year';
        document.querySelector('.period-text-enterprise').innerText = '/ 1 Year';
        
        // Show discount labels
        document.querySelector('.discount-starter').style.display = 'block';
        document.querySelector('.discount-growth').style.display = 'block';
        document.querySelector('.discount-enterprise').style.display = 'block';
    } else {
        btnM.classList.add('active');
        btnY.classList.remove('active');
        
        // Revert to Monthly Prices
        document.querySelector('.price-starter').innerText = '₹499';
        document.querySelector('.price-growth').innerText = '₹999';
        document.querySelector('.price-enterprise').innerText = '₹2,999';

        document.querySelector('.period-text-starter').innerText = '/ month';
        document.querySelector('.period-text-growth').innerText = '/ month';
        document.querySelector('.period-text-enterprise').innerText = '/ month';
        
        // Hide discount labels
        document.querySelector('.discount-starter').style.display = 'none';
        document.querySelector('.discount-growth').style.display = 'none';
        document.querySelector('.discount-enterprise').style.display = 'none';
    }
}
</script>

<?php View::endSection(); ?>
