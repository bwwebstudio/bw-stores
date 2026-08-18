<?php

use App\Core\View;
View::layout('layouts.storefront');

View::section('title'); ?><?= e($product['name']) ?> — <?= e($store['name']) ?><?php View::endSection();

View::section('content'); ?>

<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="<?= url('store/' . $store['slug']) ?>" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= url('store/' . $store['slug'] . '/products') ?>" class="text-decoration-none">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($product['name']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-5">
        <!-- Left: Product Photos Gallery -->
        <div class="col-lg-6">
            <div class="card p-2 border rounded-4 mb-3">
                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center overflow-hidden" style="aspect-ratio: 1 / 1;">
                    <?php if (!empty($product['images'][0])): ?>
                        <img id="mainProductImg" src="<?= url($product['images'][0]) ?>" alt="<?= e($product['name']) ?>" style="max-height: 480px; width: 100%; object-fit: contain;">
                    <?php else: ?>
                        <i class="bi bi-image text-muted" style="font-size: 6rem;"></i>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (count($product['images']) > 1): ?>
                <div class="d-flex gap-2">
                    <?php foreach ($product['images'] as $img): ?>
                        <div class="border rounded p-1 cursor-pointer" onclick="document.getElementById('mainProductImg').src='<?= url($img) ?>'" style="cursor: pointer; width: 70px; height: 70px;">
                            <img src="<?= url($img) ?>" class="w-100 h-100" style="object-fit: cover;">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Purchasing Details & Variants Form -->
        <div class="col-lg-6">
            <h1 class="fw-800 text-dark mb-2"><?= e($product['name']) ?></h1>

            <div class="mb-3 d-flex align-items-center gap-3">
                <span class="fs-2 fw-900 text-dark" id="displayPrice">₹<?= number_format($product['price'], 2) ?></span>
                <?php if (!empty($product['compare_price'])): ?>
                    <span class="text-muted fs-5 text-decoration-line-through">₹<?= number_format($product['compare_price'], 2) ?></span>
                <?php endif; ?>

                <?php if ($product['stock'] > 0): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">In Stock</span>
                <?php else: ?>
                    <span class="badge bg-danger">Sold Out</span>
                <?php endif; ?>
            </div>

            <?php if (!empty($product['short_description'])): ?>
                <p class="text-muted mb-4"><?= nl2br(e($product['short_description'])) ?></p>
            <?php endif; ?>

            <hr class="my-4">

            <form method="POST" action="<?= url('store/' . $store['slug'] . '/cart/add') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                <!-- Variants Selection -->
                <?php if (!empty($product['variants'])): ?>
                <div class="mb-4">
                    <label class="form-label fw-700">Select Option / Variant</label>
                    <select name="variant_id" class="form-select form-select-lg" id="variantSelect" onchange="updateVariantPrice(this)">
                        <?php foreach ($product['variants'] as $v): ?>
                            <option value="<?= $v['id'] ?>" data-price="<?= $v['price'] ?>">
                                <?= e($v['name']) ?> &bull; ₹<?= number_format($v['price'], 2) ?> (<?= $v['stock'] > 0 ? $v['stock'] . ' available' : 'Out of stock' ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <!-- Quantity -->
                <div class="mb-4">
                    <label class="form-label fw-700">Quantity</label>
                    <div class="input-group" style="width: 140px;">
                        <button type="button" class="btn btn-outline-secondary" onclick="var q = document.getElementById('qtyInput'); if(q.value > 1) q.value--;">-</button>
                        <input type="number" name="quantity" id="qtyInput" class="form-control text-center fw-700" value="1" min="1" max="99">
                        <button type="button" class="btn btn-outline-secondary" onclick="var q = document.getElementById('qtyInput'); q.value++;">+</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-store-primary btn-lg w-100 py-3 mb-3 shadow" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                    <i class="bi bi-bag-plus-fill me-2"></i> Add to Cart
                </button>
            </form>

            <!-- Product Long Description -->
            <?php if (!empty($product['description'])): ?>
            <div class="mt-4 pt-4 border-top">
                <h5 class="fw-700 text-dark mb-3">Product Details</h5>
                <div class="text-secondary small" style="line-height: 1.8;">
                    <?= nl2br(e($product['description'])) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function updateVariantPrice(select) {
    var opt = select.options[select.selectedIndex];
    if (opt && opt.dataset.price) {
        document.getElementById('displayPrice').innerText = '₹' + parseFloat(opt.dataset.price).toFixed(2);
    }
}
</script>

<?php View::endSection(); ?>
