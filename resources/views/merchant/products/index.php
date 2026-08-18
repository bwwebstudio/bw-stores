<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Products<?php View::endSection();
View::section('page_title'); ?>Product Catalog<?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-700 mb-1">Products (<?= $total ?>)</h4>
        <p class="text-muted small mb-0">Manage catalog, variants, pricing and real-time inventory.</p>
    </div>
    <a href="<?= url('dashboard/products/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Product
    </a>
</div>

<!-- Filters Bar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= url('dashboard/products') ?>" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by name or SKU..." value="<?= e($filters['search'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($filters['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="published" <?= ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="archived" <?= ($filters['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-dark w-100">Filter</button>
                <a href="<?= url('dashboard/products') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                <h5 class="fw-700 mt-3">No products found</h5>
                <p class="text-muted small">Start adding your inventory to showcase on your public storefront.</p>
                <a href="<?= url('dashboard/products/create') ?>" class="btn btn-primary btn-sm">Add First Product</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Image</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <?php if (!empty($p['images'][0])): ?>
                                    <img src="<?= url($p['images'][0]) ?>" alt="" class="rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted" style="width: 48px; height: 48px;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-600 text-dark"><?= e($p['name']) ?></div>
                                <div class="text-muted text-xs">SKU: <?= e($p['sku'] ?: 'N/A') ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= e($p['category_name'] ?: 'Uncategorized') ?></span>
                            </td>
                            <td>
                                <div class="fw-700 text-dark">₹<?= number_format($p['price'], 2) ?></div>
                                <?php if ($p['compare_price']): ?>
                                    <div class="text-muted text-xs text-decoration-line-through">₹<?= number_format($p['compare_price'], 2) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['stock'] <= 0): ?>
                                    <span class="badge bg-danger">Out of stock (0)</span>
                                <?php elseif ($p['stock'] <= $p['low_stock_limit']): ?>
                                    <span class="badge bg-warning text-dark">Low stock (<?= $p['stock'] ?>)</span>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><?= $p['stock'] ?> in stock</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['status'] === 'published'): ?>
                                    <span class="badge-status badge-success">Published</span>
                                <?php elseif ($p['status'] === 'draft'): ?>
                                    <span class="badge-status badge-warning">Draft</span>
                                <?php else: ?>
                                    <span class="badge-status badge-gray">Archived</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('dashboard/products/' . $p['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="<?= url('dashboard/products/' . $p['id'] . '/delete') ?>" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php View::endSection(); ?>
