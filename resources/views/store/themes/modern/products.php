<?php

use App\Core\View;
View::layout('layouts.storefront');

View::section('title'); ?>All Products — <?= e($store['name']) ?><?php View::endSection();

View::section('content'); ?>

<div class="bg-light py-4 border-bottom mb-4">
    <div class="container">
        <h2 class="fw-800 text-dark mb-1">Catalog</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="<?= url('store/' . $store['slug']) ?>" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Products</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card p-3 mb-4">
                <h6 class="fw-700 mb-3">Categories</h6>
                <div class="list-group list-group-flush small">
                    <a href="<?= url('store/' . $store['slug'] . '/products') ?>" class="list-group-item list-group-item-action border-0 px-0 <?= empty($filters['category_id']) ? 'fw-700 text-primary' : 'text-dark' ?>">
                        All Categories
                    </a>
                    <?php foreach ($categories as $cat): ?>
                    <a href="<?= url('store/' . $store['slug'] . '/category/' . $cat['slug']) ?>" class="list-group-item list-group-item-action border-0 px-0 <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'fw-700 text-primary' : 'text-dark' ?>">
                        <?= e($cat['name']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small">Showing <?= count($products) ?> of <?= $total ?> products</span>
            </div>

            <?php if (empty($products)): ?>
                <div class="text-center py-5 card bg-light border-0">
                    <i class="bi bi-search fs-1 text-muted"></i>
                    <h5 class="fw-700 mt-2">No products match your filter</h5>
                    <p class="text-muted small">Try searching with a different term.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $prod): ?>
                    <div class="col-6 col-md-4">
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
                                    <span class="fw-800 text-dark fs-5">₹<?= number_format($prod['price'], 2) ?></span>
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
    </div>
</div>

<?php View::endSection(); ?>
