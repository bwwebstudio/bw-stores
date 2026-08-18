<?php

use App\Core\View;
View::layout('layouts.storefront');

View::section('title'); ?><?= e($store['name']) ?> — Editorial Collection<?php View::endSection();

View::section('content'); ?>

<!-- Fashion Editorial Hero -->
<section class="py-5 text-center text-white position-relative" style="background: linear-gradient(180deg, rgba(15,23,42,0.85) 0%, rgba(15,23,42,0.95) 100%), url('<?= !empty($settings['hero_image']) ? url($settings['hero_image']) : '' ?>') center/cover; min-height: 480px; display: flex; align-items: center;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <span class="text-uppercase tracking-widest small fw-700 text-warning mb-2 d-block" style="letter-spacing: 0.25em;">Exclusive Lookbook</span>
                <h1 class="display-3 fw-900 mb-3" style="font-family: 'Playfair Display', serif;">
                    <?= e($settings['hero_title'] ?? 'The New Luxury by ' . $store['name']) ?>
                </h1>
                <p class="lead opacity-75 mb-4" style="max-width: 600px; margin: 0 auto;">
                    <?= e($settings['hero_subtitle'] ?? 'Curated for discerning taste. Explore hand-picked couture, apparel and accessories.') ?>
                </p>
                <a href="<?= url('store/' . $store['slug'] . '/products') ?>" class="btn btn-warning btn-lg px-5 py-3 fw-800 text-dark rounded-pill">
                    EXPLORE COLLECTION &rarr;
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Categories Bar -->
<?php if (!empty($categories)): ?>
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <div class="d-flex justify-content-center gap-4 flex-wrap text-uppercase small fw-700">
            <a href="<?= url('store/' . $store['slug'] . '/products') ?>" class="text-dark text-decoration-none border-bottom border-2 border-dark pb-1">All Collections</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= url('store/' . $store['slug'] . '/category/' . $cat['slug']) ?>" class="text-secondary text-decoration-none hover-dark pb-1"><?= e($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Products Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-uppercase text-muted small fw-700" style="letter-spacing: 0.2em;">Editor's Selection</span>
            <h2 class="display-6 fw-800 text-dark mt-1" style="font-family: 'Playfair Display', serif;">Featured Pieces</h2>
        </div>

        <div class="row g-4">
            <?php foreach ($featuredProducts as $prod): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card border-0 shadow-sm rounded-0">
                    <a href="<?= url('store/' . $store['slug'] . '/product/' . $prod['slug']) ?>" class="product-image-wrap rounded-0 text-decoration-none">
                        <?php if (!empty($prod['images'][0])): ?>
                            <img src="<?= url($prod['images'][0]) ?>" alt="<?= e($prod['name']) ?>">
                        <?php else: ?>
                            <i class="bi bi-image text-muted fs-1"></i>
                        <?php endif; ?>
                    </a>
                    <div class="p-3 text-center">
                        <h6 class="fw-700 mb-1" style="font-family: 'Playfair Display', serif;">
                            <a href="<?= url('store/' . $store['slug'] . '/product/' . $prod['slug']) ?>" class="text-dark text-decoration-none">
                                <?= e($prod['name']) ?>
                            </a>
                        </h6>
                        <div class="fw-800 text-dark mt-2">₹<?= number_format($prod['price'], 2) ?></div>
                        <a href="<?= url('store/' . $store['slug'] . '/product/' . $prod['slug']) ?>" class="btn btn-outline-dark btn-sm rounded-pill mt-2 px-3">
                            View Piece
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php View::endSection(); ?>
