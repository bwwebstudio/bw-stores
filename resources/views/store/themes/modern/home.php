<?php

use App\Core\View;
View::layout('layouts.storefront');

View::section('title'); ?><?= e($store['name']) ?> — Official Online Store<?php View::endSection();

View::section('content'); ?>

<!-- Hero Section -->
<section class="py-5 bg-light border-bottom position-relative overflow-hidden">
    <div class="container py-lg-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 mb-3">
                    <i class="bi bi-stars me-1"></i> New Collection
                </span>
                <h1 class="display-4 fw-900 text-dark mb-3">
                    <?= e($settings['hero_title'] ?? 'Welcome to ' . $store['name']) ?>
                </h1>
                <p class="lead text-muted mb-4">
                    <?= e($settings['hero_subtitle'] ?? 'Discover our latest premium products crafted for quality and style.') ?>
                </p>
                <div class="d-flex gap-3">
                    <a href="<?= url('store/' . $store['slug'] . '/products') ?>" class="btn btn-store-primary btn-lg px-4 py-3 shadow-sm">
                        Shop All Products <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <?php if (!empty($settings['hero_image'])): ?>
                    <img src="<?= url($settings['hero_image']) ?>" class="rounded-4 shadow-lg w-100" style="max-height: 420px; object-fit: cover;">
                <?php else: ?>
                    <div class="rounded-4 bg-white border p-5 text-center text-muted shadow-sm">
                        <i class="bi bi-bag-heart text-store-primary" style="font-size: 5rem;"></i>
                        <h4 class="fw-700 text-dark mt-3">Curated Collection</h4>
                        <p class="small text-muted mb-0">High quality items backed by fast delivery and exceptional customer support.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<?php if (!empty($categories)): ?>
<section class="py-5 bg-white border-bottom">
    <div class="container">
        <h4 class="fw-800 text-dark mb-4 text-center">Shop by Category</h4>
        <div class="row g-3 justify-content-center">
            <?php foreach ($categories as $cat): ?>
            <div class="col-6 col-md-3 col-lg-2">
                <a href="<?= url('store/' . $store['slug'] . '/category/' . $cat['slug']) ?>" class="card text-center p-3 h-100 text-decoration-none border hover-shadow">
                    <?php if (!empty($cat['image'])): ?>
                        <img src="<?= url($cat['image']) ?>" class="rounded-circle mx-auto mb-2" style="width: 54px; height: 54px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-light mx-auto mb-2 d-flex align-items-center justify-content-center text-store-primary" style="width: 54px; height: 54px;">
                            <i class="bi bi-tag fs-4"></i>
                        </div>
                    <?php endif; ?>
                    <div class="fw-700 text-dark small"><?= e($cat['name']) ?></div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Products Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-800 text-dark mb-0">Featured Products</h3>
                <p class="text-muted small mb-0">Hand-picked bestsellers for you</p>
            </div>
            <a href="<?= url('store/' . $store['slug'] . '/products') ?>" class="btn btn-sm btn-outline-dark">
                View All <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        <?php if (empty($featuredProducts)): ?>
            <div class="text-center py-5 card bg-light border-0">
                <p class="text-muted mb-0">No featured products added yet.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($featuredProducts as $prod): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card">
                        <a href="<?= url('store/' . $store['slug'] . '/product/' . $prod['slug']) ?>" class="product-image-wrap text-decoration-none">
                            <?php if (!empty($prod['images'][0])): ?>
                                <img src="<?= url($prod['images'][0]) ?>" alt="<?= e($prod['name']) ?>">
                            <?php else: ?>
                                <i class="bi bi-image text-muted fs-1"></i>
                            <?php endif; ?>
                        </a>
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <h6 class="fw-700 mb-1">
                                <a href="<?= url('store/' . $store['slug'] . '/product/' . $prod['slug']) ?>" class="text-dark text-decoration-none">
                                    <?= e($prod['name']) ?>
                                </a>
                            </h6>
                            <div class="mt-auto pt-2 d-flex justify-content-between align-items-baseline">
                                <div>
                                    <span class="fw-800 text-dark fs-5">₹<?= number_format($prod['price'], 2) ?></span>
                                    <?php if (!empty($prod['compare_price'])): ?>
                                        <span class="text-muted text-xs text-decoration-line-through ms-1">₹<?= number_format($prod['compare_price'], 2) ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= url('store/' . $store['slug'] . '/product/' . $prod['slug']) ?>" class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- New Arrivals -->
<section class="py-5 bg-light border-top">
    <div class="container">
        <h3 class="fw-800 text-dark mb-4 text-center">New Arrivals</h3>

        <div class="row g-4">
            <?php foreach ($newArrivals as $prod): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card">
                    <a href="<?= url('store/' . $store['slug'] . '/product/' . $prod['slug']) ?>" class="product-image-wrap text-decoration-none">
                        <?php if (!empty($prod['images'][0])): ?>
                            <img src="<?= url($prod['images'][0]) ?>" alt="<?= e($prod['name']) ?>">
                        <?php else: ?>
                            <i class="bi bi-image text-muted fs-1"></i>
                        <?php endif; ?>
                    </a>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h6 class="fw-700 mb-1">
                            <a href="<?= url('store/' . $store['slug'] . '/product/' . $prod['slug']) ?>" class="text-dark text-decoration-none">
                                <?= e($prod['name']) ?>
                            </a>
                        </h6>
                        <div class="mt-auto pt-2 d-flex justify-content-between align-items-baseline">
                            <div>
                                <span class="fw-800 text-dark fs-5">₹<?= number_format($prod['price'], 2) ?></span>
                            </div>
                            <a href="<?= url('store/' . $store['slug'] . '/product/' . $prod['slug']) ?>" class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php View::endSection(); ?>
