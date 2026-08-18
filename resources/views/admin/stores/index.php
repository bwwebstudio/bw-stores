<?php

use App\Core\View;
View::layout('layouts.admin');

View::section('title'); ?>All Stores<?php View::endSection();
View::section('page_title'); ?>Storefronts Directory<?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-700 mb-1">Active Storefronts (<?= $total ?>)</h4>
        <p class="text-muted small mb-0">Browse all customer-facing stores created across the BW Store platform.</p>
    </div>
    <form method="GET" action="<?= url('admin/stores') ?>" class="d-flex gap-2">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search store name or slug..." value="<?= e($search ?? '') ?>">
        <button type="submit" class="btn btn-sm btn-dark">Search</button>
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($stores)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-shop-window" style="font-size: 3rem;"></i>
                <p class="mt-2 mb-0">No storefronts found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Storefront</th>
                            <th>Merchant Owner</th>
                            <th>Products</th>
                            <th>Total Orders</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stores as $s): ?>
                        <tr>
                            <td>
                                <div class="fw-700 text-dark"><?= e($s['name']) ?></div>
                                <code class="text-xs">/store/<?= e($s['slug']) ?></code>
                            </td>
                            <td>
                                <div class="fw-600 text-dark"><?= e($s['merchant_name']) ?></div>
                                <div class="text-muted text-xs"><?= e($s['merchant_email']) ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= $s['total_products'] ?> items</span></td>
                            <td><span class="badge bg-primary-subtle text-primary border"><?= $s['total_orders'] ?> orders</span></td>
                            <td>
                                <?php if ($s['status'] === 'active'): ?>
                                    <span class="badge-status badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge-status badge-danger">Suspended</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted text-xs"><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
                            <td class="text-end">
                                <a href="<?= url('store/' . $s['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-box-arrow-up-right"></i> Visit
                                </a>
                                <a href="<?= url('admin/merchants/' . $s['merchant_id']) ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-person"></i> Merchant
                                </a>
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
