<?php

use App\Core\View;
View::layout('layouts.storefront');

View::section('title'); ?><?= e($store['name']) ?> — Enterprise Commerce Store<?php View::endSection();

View::section('content'); ?>

<!-- Business Corporate Header Banner -->
<section class="py-5 bg-dark text-white border-bottom">
    <div class="container py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-secondary rounded text-xs text-uppercase fw-700 tracking-wider mb-3">
                    <i class="bi bi-shield-check text-success"></i> Direct Manufacturer & Supplier
                </div>
                <h1 class="display-5 fw-900 text-white mb-3" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    <?= e($settings['hero_title'] ?? 'Commercial Grade Products from ' . $store['name']) ?>
                </h1>
                <p class="lead text-secondary mb-4">
                    <?= e($settings['hero_subtitle'] ?? 'Professional supply, verified quality, and fast business dispatch with GST invoices.') ?>
                </p>
                <div class="d-flex gap-3">
                    <a href="<?= url('store/' . $store['slug'] . '/products') ?>" class="btn btn-primary btn-lg px-4 py-3 fw-700 shadow">
                        Browse Full Inventory &rarr;
                    </a>
                </div>
            </div>
            <div class="col-lg-5">
                <?php if (!empty($settings['hero_image'])): ?>
                    <img src="<?= url($settings['hero_image']) ?>" class="rounded-3 shadow w-100 border border-secondary" style="max-height: 360px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-secondary bg-opacity-25 rounded-3 p-4 border border-secondary text-center">
                        <i class="bi bi-building-check text-primary fs-1"></i>
                        <h5 class="text-white mt-2">Verified Merchant Store</h5>
                        <p class="small text-secondary mb-0">Transparent pricing & instant checkout for individuals and enterprise clients.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Business Advantage Trust Badges -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <div class="row g-3 text-center small text-secondary">
            <div class="col-md-3 col-6"><i class="bi bi-check-circle-fill text-primary me-1"></i> 100% Quality Guaranteed</div>
            <div class="col-md-3 col-6"><i class="bi bi-truck text-primary me-1"></i> Fast Tracked Courier</div>
            <div class="col-md-3 col-6"><i class="bi bi-receipt text-primary me-1"></i> Official Tax Invoices</div>
            <div class="col-md-3 col-6"><i class="bi bi-headset text-primary me-1"></i> Direct Merchant Support</div>
        </div>
    </div>
</section>

<!-- Product Catalog Grid -->
<section class="py-5">
    <div class="container">
        <h3 class="fw-800 text-dark mb-4">Store Inventory</h3>

        <div class="row g-4">
            <?php foreach ($featuredProducts as $prod): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card border">
                    <a href="<?= url('store/' . $store['slug'] . '/product/' . $prod['slug']) ?>" class="product-image-wrap text-decoration-none">
                        <?php if (!empty($prod['images'][0])): ?>
                            <img src="<?= url($prod['images'][0]) ?>" alt="<?= e($prod['name']) ?>">
                        <?php else: ?>
                            <i class="bi bi-image text-muted fs-1"></i>
                        <?php endif; ?>
                    </a>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <span class="badge bg-light text-secondary border text-xs mb-1 align-self-start">SKU: <?= e($prod['sku'] ?: 'SKU-' . $prod['id']) ?></span>
                        <h6 class="fw-700 mb-1">
                            <a href="<?= url('store/' . $store['slug'] . '/product/' . $prod['slug']) ?>" class="text-dark text-decoration-none">
                                <?= e($prod['name']) ?>
                            </a>
                        </h6>
                        <div class="mt-auto pt-2 d-flex justify-content-between align-items-baseline">
                            <span class="fw-800 text-dark fs-5">₹<?= number_format($prod['price'], 2) ?></span>
                            <a href="<?= url('store/' . $store['slug'] . '/product/' . $prod['slug']) ?>" class="btn btn-sm btn-dark">Order</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php View::endSection(); ?>
